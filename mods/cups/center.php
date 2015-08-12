<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: center.php 4501 2010-08-31 22:11:01Z hajo $

$cs_lang = cs_translate('cups');

include_once 'mods/cups/defines.php';

$get_memberships = cs_sql_select(__FILE__,'members','squads_id','users_id = \''.$account['users_id'].'\'',0,0,0);
$condition = '(cs.squads_id = \''.$account['users_id'].'\' AND cp.cups_system = \'users\')';
$matchcond1 = '';
$matchcond2 = '';

if (!empty($get_memberships))
{
	$matchcond1 = 'squads1_id IN (';
	$matchcond2 = 'squads2_id IN (';
	
  $x = 0;
  $condition .= ' OR (cp.cups_system = \'teams\' AND cs.squads_id IN (';
  
  foreach($get_memberships AS $membership) {
    $x++;

    if ($x != 1) {
      $condition .= ',';
      $matchcond1 .= ',';
      $matchcond2 .= ',';
    }

    $condition .= $membership['squads_id'];
    $matchcond1 .= $membership['squads_id'];
    $matchcond2 .= $membership['squads_id'];
    $squads[] = $membership['squads_id'];
  }
  $condition .= '))';
  $matchcond1 .= ')';
  $matchcond2 .= ')';
}

$data = array();

$tables = 'cupsquads cs INNER JOIN {pre}_cups cp ON cs.cups_id = cp.cups_id LEFT JOIN {pre}_cupmatches cm ON cp.cups_id = cm.cups_id AND (cm.cupmatches_accepted1 = 0 OR cm.cupmatches_accepted2 = 0)';
$cells  = 'cs.cupsquads_checkedin AS cupsquads_checkedin, cs.cups_id AS cups_id, cp.games_id AS games_id, cp.cups_name AS cups_name, ';
$cells .= 'cp.cups_system AS cups_system, cs.squads_id AS squads_id, cp.cups_start AS cups_start, cp.cups_checkin AS cups_checkin, ';
$cells .= 'cm.cupmatches_id';
$cups_select = cs_sql_select(__FILE__,$tables,$cells,$condition,0,0,0);

$data['cups'] = array();
$data['cups_played'] = array();

$conds = array();
$conds['teams'] = !empty($get_memberships) ? '(cupmatches_accepted1 = 0 AND '.$matchcond1.') OR (cupmatches_accepted2 = 0 AND '.$matchcond2.')' : '1 = 1';
$conds['users'] = '(cupmatches_accepted1 = 0 AND squad1_id = '.intval($account['users_id']) .') OR (cupmatches_accepted2 = 0 AND squad2_id = '.intval($account['users_id']).')';

if (count($cups_select))
{
	$count_played = 0;
	$count = 0;
	foreach ($cups_select as $cup)
	{
		$cup['if']['gameicon_exists'] = file_exists('uploads/games/' . $cup['games_id'] . '.gif') ? true : false;
	  $cond = $conds[$cup['cups_system']] . ' AND cups_id = "' . $cup['cups_id'] . '"';
	  $matchid = cs_sql_select(__FILE__, 'cupmatches', 'cupmatches_id', $cond, 'cupmatches_round ASC');
		if (empty($matchid))
		{
			$time = cs_time();
			if ($cup['cups_start'] > $time && $cup['cups_checkin'] <= $time)
			{
				if (empty($cup['cupsquads_checkedin']))
	  			$cup['nextmatch'] = cs_link($cs_lang['checkin'],'cups','checkin','id='.$cup['cups_id']);
				else
	  			$cup['nextmatch'] = cs_link($cs_lang['checkout'],'cups','checkin','checkout=1&id='.$cup['cups_id']);
			}
			else
	  		$cup['nextmatch'] = $cs_lang['no_match_upcoming'];
		}
		else
	  	$cup['nextmatch'] = cs_link($cs_lang['show'],'cups','match','id='.$matchid['cupmatches_id']);
		/* check if we still have playable matches */
		if ($cup['cupmatches_id'] > 0)
			$data['cups'][$count++] = $cup;
		else /* finished cups */
			$data['cups_played'][$count_played++] = $cup;
	}
}

$data['lang']['take_part_in_cups'] = sprintf($cs_lang['take_part_in_cups'], count($data['cups']));
$data['lang']['took_part_in_cups'] = sprintf($cs_lang['took_part_in_cups'], count($data['cups_played']));

echo cs_subtemplate(__FILE__, $data, 'cups', 'center');
