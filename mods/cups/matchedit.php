<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include 'mods/cups/functions.php';

if (!empty($_POST['accept1']) OR !empty($_POST['accept2']) OR !empty($_POST['accept_submit']))
{
	/* accept a result */
  $cupmatches_id = (int) $_POST['cupmatches_id'];
  
  $cs_match = cs_sql_select(__FILE__,'cupmatches','*','cupmatches_id = ' . $cupmatches_id);

  $cup = cs_sql_select(__FILE__,'cups','cups_system, cups_brackets, cups_teams','cups_id = ' . $cs_match['cups_id']);
  
  /* determine player */
  $team = 0;
  $player = FALSE;
  if ($cup['cups_system'] == CS_CUPS_TYPE_TEAMS)
  {
  	/* check if he is part of team1 or team2 and he is a member admin */
    $cond = 'users_id = \''.$account['users_id'].'\' AND members_admin = 1 AND squads_id = \''.$cs_match['squad1_id'].'\'';
    $sql = cs_sql_count(__FILE__,'members',$cond);
    if (empty($sql))
    {
	    $cond = 'users_id = \''.$account['users_id'].'\' AND members_admin = 1 AND squads_id = \''.$cs_match['squad2_id'].'\'';
	    $sql = cs_sql_count(__FILE__,'members',$cond);
	    if (!empty($sql))
	    {
	    	$player = TRUE;
	    	$team = 2;
	    }
    }
    else
    {
    	$player = TRUE;
    	$team = 1;
    }
  }
  else
  {
  	/* check if the user id's match */
  	if ($account['users_id'] == $cs_match['squad1_id'])
  	{
    	$player = TRUE;
    	$team = 1;
  	}
  	else if ($account['users_id'] == $cs_match['squad2_id'])
  	{
    	$player = TRUE;
    	$team = 2;
    }
  }
  
  /* only accept if the player is the one who has not accepted yet */
  if ($player == TRUE && empty($cs_match['cupmatches_accepted'.$team]))
  {
    
    if (empty($_POST['accept_submit']))
    {
      $data = array();
      $data['match']['id'] = $cupmatches_id;
      
      echo cs_subtemplate(__FILE__, $data, 'cups', 'confirm');
      
    }
    else
    {
      
      $cells = array('cupmatches_accepted'.$team, 'cupmatches_accepted_time'.$team);
      $values = array('1', cs_time());
      cs_sql_update(__FILE__,'cupmatches',$cells,$values,$cupmatches_id);
      
      $cs_match['cupmatches_accepted'.$team] = 1;
      
      if (!empty($cs_match['cupmatches_accepted1']) && !empty($cs_match['cupmatches_accepted2'])) {
        
        $loser = $cs_match['cupmatches_winner'] == $cs_match['squad1_id'] ? $cs_match['squad2_id'] : $cs_match['squad1_id'];
        
				if ($cs_match['cupmatches_nextmatch'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add winner team to next match */
					cs_cups_addteam2match($cs_match['cups_id'], $cs_match['cupmatches_winner'], $cs_match['cupmatches_match'], $cs_match['cupmatches_round'], $cs_match['cupmatches_loserbracket'], $cs_match['cupmatches_nextmatch'], false);
        	$msg = $cs_lang['new_match'];
				}
				if ($cs_match['cupmatches_nextmatchlb'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add loser team to next match in LB */
					cs_cups_addteam2match($cs_match['cups_id'], $loser, $cs_match['cupmatches_match'], $cs_match['cupmatches_round'], $cs_match['cupmatches_loserbracket'], $cs_match['cupmatches_nextmatchlb'], false);
        	$msg = $cs_lang['new_match'];
				}

        /* close all defwin matches */
				cs_cups_autoclose($cs_match['cups_id']);
      }
      
      $msg = $cs_lang['successfully_confirmed'];
      if(!empty($message)) $msg .= ' ' . $message;
      
		  // clear datacache
			if (function_exists('cs_datacache_load'))
				cs_datacache_clear('cups');

      cs_redirect($msg, 'cups', 'match', 'id=' . $cupmatches_id);
      
    }
  }
  else {
    echo $cs_lang['no_access'];
  }

    
}
else if (!empty($_POST['result']) || !empty($_POST['result_submit']))
{
  
  $cupmatches_id = (int) $_POST['cupmatches_id'];
  
  $tables = 'cupmatches cm INNER JOIN {pre}_cups cp ON cm.cups_id = cp.cups_id';
  $cells = 'cp.cups_system AS cups_system';
  $system = cs_sql_select(__FILE__, $tables, $cells, 'cm.cupmatches_id = \''.$cupmatches_id.'\'');
  
  $tables  = 'cupmatches cm ';
  
  if($system['cups_system'] == CS_CUPS_TYPE_TEAMS) {
    $tables .= 'INNER JOIN {pre}_squads sq1 ON cm.squad1_id = sq1.squads_id ';
    $tables .= 'INNER JOIN {pre}_squads sq2 ON cm.squad2_id = sq2.squads_id';  
    $cells = 'cm.squad1_id AS squad1_id, cm.squad2_id AS squad2_id, ';
    $cells .='sq1.squads_name AS squad1_name, sq2.squads_name AS squad2_name, ';
  }
  else {
    $tables .= 'INNER JOIN {pre}_users usr1 ON cm.squad1_id = usr1.users_id ';
    $tables .= 'INNER JOIN {pre}_users usr2 ON cm.squad2_id = usr2.users_id ';
    $cells  = 'cm.squad1_id AS squad1_id, usr1.users_nick AS user1_nick, ';
    $cells .= 'cm.squad2_id AS squad2_id, usr2.users_nick AS user2_nick, ';
  }
  $cells .= 'cm.cupmatches_accepted1 AS cupmatches_accepted1, cm.cupmatches_accepted2 AS cupmatches_accepted2';
  
  $cs_match = cs_sql_select(__FILE__,$tables,$cells,'cm.cupmatches_id = \''.$cupmatches_id.'\'');
  
  /* determine player */
  $team = 0;
  $player = FALSE;
  if($system['cups_system'] == CS_CUPS_TYPE_TEAMS)
  {
  	/* check if he is part of team1 or team2 and he is a member admin */
    $cond = 'users_id = \''.$account['users_id'].'\' AND members_admin = 1 AND squads_id = \''.$cs_match['squad1_id'].'\'';
    $sql = cs_sql_count(__FILE__,'members',$cond);
    if (empty($sql))
    {
	    $cond = 'users_id = \''.$account['users_id'].'\' AND members_admin = 1 AND squads_id = \''.$cs_match['squad2_id'].'\'';
	    $sql = cs_sql_count(__FILE__,'members',$cond);
	    if (!empty($sql))
	    {
	    	$player = TRUE;
	    	$team = 2;
	    }
    }
    else
    {
    	$player = TRUE;
    	$team = 1;
    }
  }
  else
  {
  	/* check if the user id's match */
  	if ($account['users_id'] == $cs_match['squad1_id'])
  	{
    	$player = TRUE;
    	$team = 1;
  	}
  	else if ($account['users_id'] == $cs_match['squad2_id'])
  	{
    	$player = TRUE;
    	$team = 2;
    }
  }
  
  /* only allow this player if no result entered yet */
  if ($player == TRUE && empty($cs_match['cupmatches_accepted1']) && empty($cs_match['cupmatches_accepted2']))
  {
    if (!empty($_POST['result'])) {
      /* create form to submit result */
      $data = array();
      $data['match']['id'] = $cupmatches_id;
      $data['match']['teamnr'] = $team;
      
      if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS)
      {
        $data['match']['team1_name'] = $cs_match['squad1_name'];
        $data['match']['team2_name'] = $cs_match['squad2_name'];
      } else {
        $data['match']['team1_name'] = $cs_match['user1_nick'];
        $data['match']['team2_name'] = $cs_match['user2_nick'];
      }
      $data['match']['team1_id'] = $cs_match['squad1_id'];
      $data['match']['team2_id'] = $cs_match['squad2_id'];
      
      echo cs_subtemplate(__FILE__, $data, 'cups', 'enter_result');

    } else {
      /* we got the result */
      $cs_cups['cupmatches_winner'] = (int) $_POST['cupmatches_winner'];
      $cs_cups['cupmatches_score1'] = (int) $_POST['cupmatches_score1'];
      $cs_cups['cupmatches_score2'] = (int) $_POST['cupmatches_score2'];
      $cs_cups['cupmatches_accepted'.$team] = '1';
    	$cs_cups['cupmatches_accepted_time'.$team] = cs_time();
    	
      $error = '';
      
      /* check if the winner is one of the teams */
      if (!in_array($cs_cups['cupmatches_winner'], array($cs_match['squad1_id'], $cs_match['squad2_id'])))
        $error .= cs_html_br(1) . $cs_lang['no_winner'];
      
      if (empty($error))
      {
        $cells = array_keys($cs_cups);
        $values = array_values($cs_cups);
        
        cs_sql_update(__FILE__,'cupmatches',$cells,$values,$cupmatches_id);
        
			  // clear datacache
				if (function_exists('cs_datacache_load'))
					cs_datacache_clear('cups');
	
        cs_redirect($cs_lang['result_successful'], 'cups', 'center');
      } else {
        cs_redirect($cs_lang['error_occured'] . $error, 'cups', 'center');
      }
    }
  }
  else
  {
    cs_redirect($cs_lang['no_access'], 'cups', 'center');
  }
}
elseif(!empty($_POST['adminedit']) || !empty($_POST['admin_submit']))
{
  if ($account['access_cups'] < 4)
  {
    echo cs_redirect($cs_lang['no_access'], 'cups', 'match', 'id=' . $cupmatches_id);
  }
  else
  {
    $cupmatches_id = (int) $_POST['cupmatches_id'];
    
    if (!empty($_POST['admin_submit']))
    {
  		$cup_match = cs_sql_select(__FILE__,'cupmatches','*','cupmatches_id = ' . $cupmatches_id);
      $cs_match = array();
      $cs_match['cupmatches_score1'] = (int) $_POST['cupmatches_score1'];
      $cs_match['cupmatches_score2'] = (int) $_POST['cupmatches_score2'];
    
      if($_POST['cupmatches_score1'] > $_POST['cupmatches_score2']) {
        $cs_match['cupmatches_winner'] = $cup_match['squad1_id'];
      }
      else if($_POST['cupmatches_score2'] > $_POST['cupmatches_score1']) {
        $cs_match['cupmatches_winner'] = $cup_match['squad2_id'];
      }
			else
			{
        $error = cs_html_br(1) . $cs_lang['no_winner'];
        cs_redirect($cs_lang['error_occured'] . $error, 'cups', 'match', 'id='.$cupmatches_id);
      }
      
      $cs_match['cupmatches_accepted1'] = empty($_POST['cupmatches_accepted1']) ? 0 : 1;
      $cs_match['cupmatches_accepted2'] = empty($_POST['cupmatches_accepted2']) ? 0 : 1;
      
      $cells = array_keys($cs_match);
      $values = array_values($cs_match);
      
      cs_sql_update(__FILE__,'cupmatches',$cells,$values,$cupmatches_id);
        
      // Check for new round
        
      if (!empty($cs_match['cupmatches_accepted1']) && !empty($cs_match['cupmatches_accepted2'])) {
				$cs_match = cs_sql_select(__FILE__, 'cupmatches', '*', 'cupmatches_id = '.$cupmatches_id, 0, 0, 1);
        
        $loser = $cs_match['cupmatches_winner'] == $cup_match['squad1_id'] ? $cup_match['squad2_id'] : $cup_match['squad1_id'];
        
				if ($cs_match['cupmatches_nextmatch'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add winner team to next match */
					cs_cups_addteam2match($cs_match['cups_id'], $cs_match['cupmatches_winner'], $cs_match['cupmatches_match'], $cs_match['cupmatches_round'], $cs_match['cupmatches_loserbracket'], $cs_match['cupmatches_nextmatch'], true);
        	$msg = $cs_lang['new_match'];
				}
				if ($cs_match['cupmatches_nextmatchlb'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add loser team to next match in LB */
					cs_cups_addteam2match($cs_match['cups_id'], $loser, $cs_match['cupmatches_match'], $cs_match['cupmatches_round'], $cs_match['cupmatches_loserbracket'], $cs_match['cupmatches_nextmatchlb'], true);
        	$msg = $cs_lang['new_match'];
				}
        
        /* close all defwin matches */
				cs_cups_autoclose($cs_match['cups_id']);
      }
        
      echo $cs_lang['changes_done'] . '. ';
      
      $message = $cs_lang['changes_done'] . '. ';
      if(!empty($msg)) $message .= ' ' . $msg;
      
		  // clear datacache
			if (function_exists('cs_datacache_load'))
				cs_datacache_clear('cups');

      cs_redirect($message, 'cups', 'match', 'id=' . $cupmatches_id);
      
    }
    else
    {
      $tables = 'cupmatches cm INNER JOIN {pre}_cups cp ON cm.cups_id = cp.cups_id';
      $cells = 'cp.cups_system AS cups_system';
      $system = cs_sql_select(__FILE__,$tables,$cells,'cm.cupmatches_id = ' . $cupmatches_id);
      
      $tables  = 'cupmatches cm ';
      
      if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS)
      {
        $tables .= 'LEFT JOIN {pre}_squads sq1 ON cm.squad1_id = sq1.squads_id ';
        $tables .= 'LEFT JOIN {pre}_squads sq2 ON cm.squad2_id = sq2.squads_id ';  
        $tables .= 'LEFT JOIN {pre}_cupsquads cs1 ON cm.squad1_id = cs1.squads_id ';
        $tables .= 'LEFT JOIN {pre}_cupsquads cs2 ON cm.squad2_id = cs2.squads_id ';
        $cells = 'cm.squad1_id AS squad1_id, cm.squad2_id AS squad2_id, ';
        $cells .= 'sq1.squads_name AS squad1_name, sq2.squads_name AS squad2_name, ';
      }
      else
      {
        $tables .= 'LEFT JOIN {pre}_users usr1 ON cm.squad1_id = usr1.users_id ';
        $tables .= 'LEFT JOIN {pre}_users usr2 ON cm.squad2_id = usr2.users_id ';
        $cells  = 'cm.squad1_id AS squad1_id, usr1.users_nick AS user1_nick, ';
        $cells .= 'cm.squad2_id AS squad2_id, usr2.users_nick AS user2_nick, ';
      }
      
      $cells .= 'cm.cupmatches_accepted1 AS cupmatches_accepted1, ';
      $cells .= 'cm.cupmatches_accepted2 AS cupmatches_accepted2, ';
      $cells .= 'cm.cupmatches_accepted_time1 AS cupmatches_accepted_time1, ';
      $cells .= 'cm.cupmatches_accepted_time2 AS cupmatches_accepted_time2, ';
      $cells .= 'cm.cupmatches_score1 AS cupmatches_score1, ';
      $cells .= 'cm.cupmatches_score2 AS cupmatches_score2';
       
      $data = array();
      
      $data['match'] = cs_sql_select(__FILE__,$tables,$cells,'cm.cupmatches_id = ' . $cupmatches_id);
			if ($data['match']['squad1_id'] == CS_CUPS_TEAM_UNKNOWN || $data['match']['squad2_id'] == CS_CUPS_TEAM_UNKNOWN)
			{
				echo $cs_lang['edit_denied'];
				return;
			}
      
      if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS)
      {
       	$data['match']['team1_id'] = $data['match']['squad1_id'];
        $data['match']['team2_id'] = $data['match']['squad2_id'];
      	if (empty($data['match']['squad1_id']))
	        $data['match']['team1_name'] = $cs_lang['bye'];
      	else
        	$data['match']['team1_name'] = empty($data['match']['squad1_name']) ? '? ID:'.$data['match']['squad1_id'] : cs_secure($data['match']['squad1_name']);
      	if (empty($data['match']['squad2_id']))
	        $data['match']['team2_name'] = $cs_lang['bye'];
      	else
        	$data['match']['team2_name'] = empty($data['match']['squad2_name']) ? '? ID:'.$data['match']['squad2_id'] : cs_secure($data['match']['squad2_name']);
      }
      else
      {
        $data['match']['team1_id'] = $data['match']['squad1_id'];
        $data['match']['team2_id'] = $data['match']['squad2_id'];
      	if (empty($data['match']['squad1_id']))
	        $data['match']['team1_name'] = $cs_lang['bye'];
      	else
	        $data['match']['team1_name'] = $data['match']['user1_nick'];
      	if (empty($data['match']['squad2_id']))
	        $data['match']['team2_name'] = $cs_lang['bye'];
      	else
        	$data['match']['team2_name'] = $data['match']['user2_nick'];
      }
      
      $data['match']['id'] = $cupmatches_id;
      $data['checked']['team1'] = empty($data['match']['cupmatches_accepted1']) ? '' : ' checked="checked"';
      $data['checked']['team2'] = empty($data['match']['cupmatches_accepted2']) ? '' : ' checked="checked"';
      $data['match']['time1'] = empty($data['match']['cupmatches_accepted_time1']) ? '-' : date('Y-m-d @H:i', $data['match']['cupmatches_accepted_time1']);
      $data['match']['time2'] = empty($data['match']['cupmatches_accepted_time2']) ? '-' : date('Y-m-d @H:i', $data['match']['cupmatches_accepted_time2']);
			global $com_lang;
			$data['match']['dtcon'] = $com_lang['dtcon'];
      
      echo cs_subtemplate(__FILE__, $data, 'cups', 'adminedit');
    }
  }
}
