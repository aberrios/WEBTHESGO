<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include_once 'mods/cups/functions.php';

$data = array();
$time_now = cs_time();

$cups_id = (int) $_GET['id'];

$tables = 'cups cp LEFT JOIN {pre}_games gms ON cp.games_id = gms.games_id';
$cells = 'cp.cups_name AS cups_name, gms.games_name AS games_name, cp.cups_system AS cups_system,
          cp.cups_teams AS cups_teams, cp.cups_text AS cups_text, cp.cups_start AS cups_start, cp.cups_checkin AS cups_checkin,
          cp.games_id AS games_id, cp.cups_brackets AS cups_brackets, cp.cups_access AS cups_access, cp.cups_id AS cups_id';
$data['cup'] = cs_sql_select(__FILE__,$tables,$cells,'cp.cups_id = '.$cups_id.'');

if (empty($data['cup'])) {
  cs_redirect($cs_lang['no_selection'], 'cups', 'list');
}

if ($account['access_cups'] < $data['cup']['cups_access'] || $data['cup']['cups_access'] == 0)
{
	echo $cs_lang['access_denied'];
	return;
}

$data['lang']['max_participants'] = $cs_lang['max_'.$data['cup']['cups_system']];
$data['lang']['registered_participants'] = $cs_lang['registered_'.$data['cup']['cups_system']];
$data['lang']['checkedin_participants'] = $cs_lang['checkedin_'.$data['cup']['cups_system']];

$data['var']['message'] = cs_getmsg();

$data['cup']['system'] = $data['cup']['cups_system'] == CS_CUPS_TYPE_TEAMS ? $cs_lang['team_vs_team'] : $cs_lang['user_vs_user'];
switch ($data['cup']['cups_brackets'])
{
default:
case CS_CUPS_SYSTEM_KO: $data['cup']['kind'] = $cs_lang['ko']; break;
case CS_CUPS_SYSTEM_LB: $data['cup']['kind'] = $cs_lang['brackets']; break;
case CS_CUPS_SYSTEM_KO3RD: $data['cup']['kind'] = $cs_lang['ko3rd']; break;
}
$data['cup']['reg'] = cs_sql_count(__FILE__,'cupsquads','cups_id = ' . $cups_id);
$data['cup']['checkedin'] = cs_sql_count(__FILE__,'cupsquads','cupsquads_checkedin = 1 AND cups_id = ' . $cups_id);
$data['cup']['percentage_reg'] = (int) (100 * $data['cup']['reg'] / $data['cup']['cups_teams']);
if ($data['cup']['percentage_reg'] > 100)
	$data['cup']['percentage_reg'] = 100;
$data['cup']['percentage_open'] = 100 - $data['cup']['percentage_reg'];
$data['cup']['start_date'] = cs_date('unix',$data['cup']['cups_start'],1);
$data['cup']['checkin_date'] = cs_date('unix',$data['cup']['cups_checkin'],1);
$data['cup']['cups_text'] = cs_secure($data['cup']['cups_text'],1,1);

if (file_exists('uploads/games/' . $data['cup']['games_id'] . '.gif'))
	$data['cup']['game'] = cs_html_img('uploads/games/' . $data['cup']['games_id'] . '.gif');
else
  $data['cup']['game'] = '';
  
$where = "games_id = '" . $data['cup']['games_id'] . "'";
$cs_game = cs_sql_select(__FILE__,'games','games_name, games_id',$where);
$id = 'id=' . $cs_game['games_id'];
$data['cup']['game'] .= ' ' . cs_link($cs_game['games_name'],'games','view',$id);
 
$data['if']['running'] = false;

$max_rounds = cs_cups_log($data['cup']['cups_teams']);
$find_round = ($data['cup']['cups_brackets'] == CS_CUPS_SYSTEM_LB ? 0 : $max_rounds);
$matchcells = 'cupmatches_round, cupmatches_score1, cupmatches_score2, cupmatches_winner, squad1_id, ';
$matchcells .= 'squad2_id, cupmatches_accepted1, cupmatches_accepted2';
$matchsel = cs_sql_select(__FILE__,'cupmatches',$matchcells,'cups_id = ' . $cups_id.' AND cupmatches_loserbracket = 0 AND cupmatches_round = '.$find_round , 0, 0, 1);

if (empty($matchsel)) {
	/* we have no cupmatches yet */
  $data['cup']['status'] = $cs_lang['upcoming'];
  $data['cup']['rounds'] = '-';
}
else {
  if(!empty($matchsel['cupmatches_accepted1']) && !empty($matchsel['cupmatches_accepted2'])) {
		/* we have cupmatches, and the final is already closed */
    $data['cup']['status'] = $cs_lang['finished'];  
	  $data['cup']['rounds'] = '-';
  }
  else {
  	/* we have cupmatches, and the final is not already closed */
    $data['if']['running'] = true;
    $data['cup']['status'] = $cs_lang['playing'] . ', ' . $cs_lang['round'];
    $lowestmatch = cs_sql_select(__FILE__,'cupmatches',$matchcells,'cups_id = ' . $cups_id.' AND cupmatches_loserbracket = 0 AND (cupmatches_accepted1 = 0 OR cupmatches_accepted2 = 0) AND cupmatches_round > 0', 'cupmatches_round ASC', 0, 1);
		if (!empty($lowestmatch))
		{
			/* we have lower round WB matches */
    	$data['cup']['rounds'] = $max_rounds + ($data['cup']['cups_brackets'] == CS_CUPS_SYSTEM_LB ? 2 : 1) - $lowestmatch['cupmatches_round'];
    	$data['cup']['status_rounds'] = $lowestmatch['cupmatches_round'];
		}
    else
    {
    	/* we only have final in WB */
    	$data['cup']['rounds'] = 1;
    	$data['cup']['status_rounds'] = $max_rounds + ($data['cup']['cups_brackets'] == CS_CUPS_SYSTEM_LB ? 1 : 0);
    }
  }
}

if (empty($matchsel['cupmatches_winner']) || !empty($data['if']['running'])) {
  $data['cup']['winner'] = '-';
} else {
  if ($data['cup']['cups_system'] == CS_CUPS_TYPE_TEAMS) {
    $winnername = cs_sql_select(__FILE__,'squads','squads_name','squads_id = \''.$matchsel['cupmatches_winner'].'\'');
    $data['cup']['winner'] = cs_link($winnername['squads_name'],'squads','view','id='.$matchsel['cupmatches_winner']);
  } else {
    $winnername = cs_sql_select(__FILE__,'users','users_nick, users_active, users_delete, users_country','users_id = \''.$matchsel['cupmatches_winner'].'\'');
    $data['cup']['winner'] = cs_html_img('symbols/countries/'.$winnername['users_country'].'.png').'&nbsp;'.cs_user($matchsel['cupmatches_winner'],$winnername['users_nick'], $winnername['users_active'], $winnername['users_delete']);
  }
}

$data['cup']['extended'] = '-';

if (empty($login['mode'])) {
  $data['cup']['extended'] = cs_link($cs_lang['login'],'users','login');
}

if ($time_now > $data['cup']['cups_start'])
  $data['cup']['extended'] = $cs_lang['cup_started'];
elseif ($data['cup']['cups_checkin'] <= $time_now || $data['cup']['checkedin'] < $data['cup']['cups_teams']) {
	$checkedin = 0;
  if ($data['cup']['cups_system'] == CS_CUPS_TYPE_TEAMS) {

    $membership = cs_sql_count(__FILE__,'members',"users_id = '" . $account['users_id'] . "' AND members_admin = '1'");
    
    if(!empty($membership)) {
      $tables = 'cupsquads csq INNER JOIN {pre}_members mem ON csq.squads_id = mem.squads_id';
      $where = "mem.users_id = '" . $account['users_id'] . "' AND mem.members_admin = 1 AND csq.cups_id = " . $cups_id;
      $participant = cs_sql_count(__FILE__,$tables,$where);
      $check = cs_sql_select(__FILE__,$tables,'csq.cupsquads_checkedin', $where, 0, 0, 1);
			if (!empty($check))
				$checkedin = $check['cupsquads_checkedin'];
    }
  } else {
			// TODO checkin
    $participant = cs_sql_count(__FILE__,'cupsquads','cups_id = ' . $cups_id . ' AND squads_id = '.$account['users_id']);
    $check = cs_sql_select(__FILE__,'cupsquads','cupsquads_checkedin', 'cups_id = ' . $cups_id . ' AND squads_id = '.$account['users_id'], 0, 0, 1);
		if (!empty($check))
			$checkedin = $check['cupsquads_checkedin'];
  }
  $started = cs_sql_count(__FILE__,'cupmatches','cups_id = ' . $cups_id);

  if($account['access_cups'] >= 2 && $time_now < $data['cup']['cups_start'] && empty($started)) {
    
    if(!empty($participant)) {
			if ($time_now >= $data['cup']['cups_checkin'])
			{
				if (empty($checkedin))
    	  	$data['cup']['extended'] = cs_link($cs_lang['checkin'],'cups','checkin','id=' . $cups_id);
				else
    	  	$data['cup']['extended'] = cs_link($cs_lang['checkout'],'cups','checkin','checkout=1&id=' . $cups_id);
			}
			else
      	$data['cup']['extended'] = $cs_lang['join_done'];
    }
    elseif(!empty($membership) || $data['cup']['cups_system'] == CS_CUPS_TYPE_USERS) {
      $data['cup']['extended'] = cs_link($cs_lang['join'],'cups','join','id=' . $cups_id);
    }
    else {
      $data['cup']['extended'] = cs_link($cs_lang['need_squad'],'squads','center');
    }
  }
} else {
  $data['cup']['extended'] = $cs_lang['already_full'];
  
  if ($data['cup']['cups_system'] == CS_CUPS_TYPE_TEAMS) {

    $membership = cs_sql_count(__FILE__,'members','users_id = ' . $account['users_id'] . ' AND members_admin = 1');
    
    if(!empty($membership)) {
      $tables = 'cupsquads csq INNER JOIN {pre}_members mem ON csq.squads_id = mem.squads_id';
      $where = "mem.users_id = '" . $account['users_id'] . "' AND mem.members_admin = 1 AND csq.cups_id = '" . $cups_id . "'";
      $participant = cs_sql_count(__FILE__, $tables, $where);
    }
  } else {
    $participant = cs_sql_count(__FILE__,'cupsquads','cups_id = "' . $cups_id . '" AND squads_id = "' . $account['users_id'] . '"');
  }
  if(!empty($participant)) {
    $data['cup']['extended'] = $cs_lang['join_done'];
  }
}

$data['cup']['match_url'] = cs_url('cups','matchlist','where='.$cups_id);
$data['if']['teams'] = false;
$data['if']['players'] = false;

if($data['cup']['cups_system'] == CS_CUPS_TYPE_TEAMS)
{
	$tables = 'cupsquads cup LEFT JOIN {pre}_squads team ON cup.squads_id = team.squads_id LEFT JOIN {pre}_clans clan ON clan.clans_id = team.clans_id';
	$cells = 'cup.squads_id, cup.cupsquads_id, cup.cupsquads_checkedin, cup.cupsquads_seed, cup.cupsquads_autoseed, team.squads_name, clan.clans_tag';
  $squads_ids = cs_sql_select(__FILE__, $tables, $cells,'cups_id = ' . $cups_id,'team.squads_name',0,0);
  $run=0;
  $squads = array();
  
  if (!empty($squads_ids))
  {
    foreach($squads_ids as $squads_run)
    {
    	$squads[$run]['seed'] = empty($squads_run['cupsquads_autoseed']) ? $squads_run['cupsquads_seed'] : '-';
      if (empty($squads_run['squads_name']))
      {
        $squads[$run]['squads_id'] = CS_CUPS_TEAM_BYE;
        $squads[$run]['squads_name'] = '? ID:'.$squads_run['squads_id'];
      }
      else
      {
        $squads[$run]['squads_id'] = $squads_run['squads_id'];
        $squads[$run]['squads_name'] = $squads_run['clans_tag'].' - '.$squads_run['squads_name'];
      }
      $run++;
    }
  }
  if(!empty($squads)) {
    $data['if']['teams'] = true;
    
    $run=0;
    foreach($squads as $squads_run) {
    	$data['squads'][$run]['seed'] = $squads_run['seed'];
      $data['squads'][$run]['name'] = empty($squads_run['squads_id']) ? cs_secure($squads_run['squads_name']) : cs_link(cs_secure($squads_run['squads_name']),'squads','view','id=' . $squads_run['squads_id']);
      $data['squads'][$run]['if']['checked'] = empty($squads_run['cupsquads_checkedin']) ? false : true;
    
      $part_tab = 'members mem INNER JOIN {pre}_users usr ON mem.users_id = usr.users_id';
      $part_cells = 'mem.members_admin AS members_admin, usr.users_nick AS users_nick, usr.users_id AS users_id, usr.users_active AS users_active, usr.users_country AS users_country';
      $where = "mem.squads_id = '" . $squads_run['squads_id'] . "'";
      $members = cs_sql_select(__FILE__,$part_tab,$part_cells,$where,'mem.members_order',0,0);
      
      if(empty($members)) {
        $data['loop']['members'] = '';
        $data['stop']['members'] = '';
        $data['squads'][$run]['members']['country'] = '';
        $data['squads'][$run]['members']['name'] = '';
        $data['squads'][$run]['members']['dot'] = '';
      }
      else {
        $members_loop = count($members);
        for($run_2=0; $run_2<$members_loop; $run_2++) {
          $users_nick = cs_secure($members[$run_2]['users_nick']);
          $users_link = cs_user($members[$run_2]['users_id'],$members[$run_2]['users_nick'], $members[$run_2]['users_active']);
           
          if(!empty($members[$run_2]['members_admin'])) {
            $users_link = cs_html_big(1) . $users_link . cs_html_big(0);
          }
          $all = $run_2 == ($members_loop - 1) ? '' : ', ';
      
          if(empty($members[$run_2]['users_country'])) {
            $country = '-';
          }
          else {
            $url = 'symbols/countries/' . $members[$run_2]['users_country'] . '.png';
            $country =  cs_html_img($url,11,16);
          }
      
          $data['squads'][$run]['members'][$run_2]['country'] = $country;
          $data['squads'][$run]['members'][$run_2]['name'] = $users_link;
          $data['squads'][$run]['members'][$run_2]['dot'] = $all;
          
        }
      }
      $run++;
    }
    
  }  
}
else {
  $tables = 'cupsquads cs INNER JOIN {pre}_users usr ON cs.squads_id = usr.users_id';
  $cells = 'cs.squads_id AS users_id, cs.cupsquads_checkedin, cs.cupsquads_seed, cs.cupsquads_autoseed, usr.users_nick AS users_nick, usr.users_active AS users_active, usr.users_country AS users_country';
  $select = cs_sql_select(__FILE__,$tables,$cells,'cs.cups_id = ' . $cups_id,'cupsquads_checkedin DESC, usr.users_nick ASC',0,0);
  
  if(!empty($select)) {
    $data['if']['players'] = true;
    
    $run = 0;
    foreach ($select AS $user) {
    	$data['cup_loop'][$run]['seed'] = empty($user['cupsquads_autoseed']) ? $user['cupsquads_seed'] : '-';
      $data['cup_loop'][$run]['players'] = cs_user($user['users_id'],$user['users_nick'], $user['users_active']);
      $data['cup_loop'][$run]['playersflag'] = cs_html_img('symbols/countries/'.$user['users_country'].'.png');
      $data['cup_loop'][$run]['if']['checked'] = empty($user['cupsquads_checkedin']) ? false : true;
      $run++;
    }
  }
}

echo cs_subtemplate(__FILE__,$data,'cups','view');
