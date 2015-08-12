<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include_once('mods/cups/defines.php');

$cups_id = (int) $_GET['where'];
$start = empty($_GET['start']) ? 0 : (int) $_GET['start'];

$cs_sort[1] = 'squads_name ASC';
$cs_sort[2] = 'squads_name DESC';
$cs_sort[3] = 'cupsquads_time ASC';
$cs_sort[4] = 'cupsquads_time DESC';
$cs_sort[5] = 'cupsquads_seed ASC, cupsquads_autoseed ASC';
$cs_sort[6] = 'cupsquads_seed DESC, cupsquads_autoseed DESC';

$sort = empty($_GET['sort']) ? 3 : (int) $_GET['sort'];
$order = $cs_sort[$sort];

$data = array();
$data['count']['all'] = cs_sql_count(__FILE__,'cupsquads','cups_id = ' . $cups_id);
$data['pages']['list'] = cs_pages('cups','teams',$data['count']['all'],$start,$cups_id,$sort);
$data['sort']['name'] = cs_sort('cups', 'teams', $start, $cups_id, 1, $sort);
$data['sort']['join'] = cs_sort('cups','teams',$start,$cups_id,3,$sort);
$data['sort']['seed'] = cs_sort('cups','teams',$start,$cups_id,5,$sort);
$data['var']['message'] = cs_getmsg();
$data['cup']['id'] = $cups_id;

$cups_system = cs_sql_select(__FILE__,'cups','cups_system, cups_access','cups_id = ' . $cups_id);

if ($account['access_cups'] < $cups_system['cups_access'] || $cups_system['cups_access'] == 0)
{
	echo $cs_lang['access_denied'];
	return;
}

$cups_system = $cups_system['cups_system'];


if ($cups_system == CS_CUPS_TYPE_USERS)
{
  // user
  $tables  = 'cupsquads cs INNER JOIN {pre}_';
  $tables .= 'users usr ON cs.squads_id = usr.users_id';
  $cells   = 'cs.cupsquads_id AS cupsquads_id, cs.cupsquads_time AS cupsquads_time, cs.squads_id AS squads_id, ';
  $cells  .= 'cs.cupsquads_seed AS cupsquads_seed, cs.cupsquads_autoseed AS cupsquads_autoseed, ';
	$cells  .= 'cs.cupsquads_checkedin AS cupsquads_checkedin, ';
  $cells  .= 'usr.users_nick AS squads_name, usr.users_active AS users_active, usr.users_delete AS users_delete';
  $mod     = 'users';
  $data['teams'] = cs_sql_select(__FILE__,$tables,$cells,'cups_id = ' . $cups_id,$order,$start,$account['users_limit']);
  if(!empty($data['teams']))
  {
		$count = count($data['teams']);
    for ($run = 0; $run < $count; $run++)
    {
			$checkinlink = cs_link($cs_lang['checkin'], 'cups', 'teamcheckin', 'cups_id='.$cups_id.'&cupsquads_id='.$data['teams'][$run]['cupsquads_id']);
			$checkoutlink = cs_link($cs_lang['checkout'], 'cups', 'teamcheckin', 'cups_id='.$cups_id.'&checkout=1&cupsquads_id='.$data['teams'][$run]['cupsquads_id']);
  		$data['teams'][$run]['cupsquads_checkin'] = empty($data['teams'][$run]['cupsquads_checkedin']) ?
				$checkinlink : $checkoutlink;
		}
	}
} else {
  // squad
  $tables  = 'cupsquads cup LEFT JOIN {pre}_squads team ON cup.squads_id = team.squads_id'
  					.' LEFT JOIN {pre}_clans clan ON clan.clans_id = team.clans_id';
  $cells   = 'cup.squads_id, cup.cupsquads_id, cup.cupsquads_time, cup.cupsquads_seed, cup.cupsquads_autoseed,'
  					.' cup.cupsquads_checkedin, team.squads_name, clan.clans_tag';
  $squads_ids = cs_sql_select(__FILE__, $tables, $cells,'cup.cups_id = ' . $cups_id,$order,$start,$account['users_limit']);
  $run=0;
  if(!empty($squads_ids))
  {
    foreach($squads_ids as $squads_run)
    {
      if (empty($squads_run['squads_name'])){
        // squad wurde bereits gel&ouml;scht
        $data['teams'][$run]['squads_name'] = '? ID: ' . $squads_run['squads_id'];
        $data['teams'][$run]['squads_id'] = 0;
      }
      else {
        $data['teams'][$run]['squads_id'] = $squads_run['squads_id'];
        $data['teams'][$run]['squads_name'] = $squads_run['clans_tag'].' - '.$squads_run['squads_name'];
      }
      $data['teams'][$run]['cupsquads_time'] = $squads_run['cupsquads_time'];
      $data['teams'][$run]['cupsquads_id'] = $squads_run['cupsquads_id'];
      $data['teams'][$run]['cupsquads_seed'] = $squads_run['cupsquads_seed'];
      $data['teams'][$run]['cupsquads_autoseed'] = $squads_run['cupsquads_autoseed'];
			$checkinlink = cs_link($cs_lang['checkin'], 'cups', 'teamcheckin', 'cups_id='.$cups_id.'&cupsquads_id='.$squads_run['cupsquads_id']);
			$checkoutlink = cs_link($cs_lang['checkout'], 'cups', 'teamcheckin', 'cups_id='.$cups_id.'&checkout=1&cupsquads_id='.$squads_run['cupsquads_id']);
      $data['teams'][$run]['cupsquads_checkin'] = empty($squads_run['cupsquads_checkedin']) ?
							$checkinlink : $checkoutlink;
      $run++;
    }
  }
  $mod     = 'squads';
}

$count_teams = empty($data['teams']) ? 0 : count($data['teams']);
$data['if']['teams_loop'] = empty($count_teams) ? FALSE : TRUE;
for ($i = 0; $i < $count_teams; $i++)
{
  $data['teams'][$i]['join'] = cs_date('unix', $data['teams'][$i]['cupsquads_time'],1);
  $data['teams'][$i]['cupsquads_autoseed'] = !empty($data['teams'][$i]['cupsquads_autoseed']) ? $cs_lang['auto'] : '';
  $data['teams'][$i]['cupsquads_seed'] = $data['teams'][$i]['cupsquads_seed'] > CS_CUPS_MAX_SEED ? '-' : $data['teams'][$i]['cupsquads_seed'];
  if ($cups_system == CS_CUPS_TYPE_TEAMS)
    $data['teams'][$i]['team'] = empty($data['teams'][$i]['squads_id']) ? cs_secure($data['teams'][$i]['squads_name']) : cs_link(cs_secure($data['teams'][$i]['squads_name']),'squads','view','id=' . $data['teams'][$i]['squads_id']);
  else
    $data['teams'][$i]['team'] = cs_user($data['teams'][$i]['squads_id'],$data['teams'][$i]['squads_name'], $data['teams'][$i]['users_active'], $data['teams'][$i]['users_delete']);
}

echo cs_subtemplate(__FILE__, $data, 'cups', 'teams');
