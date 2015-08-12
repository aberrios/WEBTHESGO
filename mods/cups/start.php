<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include_once 'mods/cups/functions.php';

if (!empty($_POST['reduce'])) {
  
  $id = (int) $_POST['id'];
  
  $cs_cups['cups_teams'] = (int) $_POST['teams'];
  
  $cells = array_keys($cs_cups);
  $values = array_values($cs_cups);
  
  cs_sql_update(__FILE__,'cups',$cells,$values,$id);
  
}

if (!empty($_POST['start']) || !empty($_POST['reduce'])) {
  
  $id = (int) $_POST['id'];

	
  $maxteams = cs_sql_select(__FILE__,'cups','cups_teams, cups_brackets','cups_id = ' . $id);
  $halfmax = $maxteams['cups_teams'] / 2;
  
	/* first reseed the seeds */
	cs_cups_reseed($id);
  /* select all checked in teams by seeding, then by join date, with a maximum of the cup size */
  $select = cs_sql_select(__FILE__,'cupsquads','squads_id, cupsquads_autoseed, cupsquads_seed','cupsquads_checkedin = 1 AND cups_id = ' . $id,'cupsquads_autoseed ASC, cupsquads_seed ASC, cupsquads_time ASC',0,$maxteams['cups_teams']);
  
  if (!empty($select))
  {
  	/* define the seedpositions for all squads */
  	$numteams = count($select);
  	$seedpos = array();
  	$random = array();
  	$seed = 1;
  	/* first position all seeded teams */
	  foreach ($select as $squad)
	  {
	  	if ($seed > $maxteams['cups_teams'])
	  		break;
	  	if ($squad['cupsquads_autoseed'] == 0)
	  	{
	  		$seedpos[$seed++] = $squad['squads_id'];
	  	}
	  	else
	  	{
	  		$random[] = $squad['squads_id'];
	  	}
	  }
	  /* now that we have all seeded teams in the first positions
	   * add the rest of the teams randomly behind the seeded teams
	   */
	  if ($seed <= $maxteams['cups_teams'])
	  {
	  	shuffle($random);
		  foreach ($random as $teamleft)
		  {
				$seedpos[$seed++] = $teamleft;
		  }
	  }
	  /* fill the rest of the teams with free-wins */
	  while ($seed <= $maxteams['cups_teams'])
	  {
	  	$seedpos[$seed++] = 0;
	  }
	  
	  /* we have now all the teams */

		/* now we generate all matches */
		$matches = cs_cups_generate($seedpos, $maxteams['cups_teams'], $maxteams['cups_brackets']); 
  }
  if (!empty($matches))
  {
  	foreach ($matches as $matchnr => $match)
  	{
      $cs_cups = array();
      $cs_cups['cups_id'] = $id;
      $cs_cups['squad1_id'] = (isset($match['squad_id1']) ? $match['squad_id1'] : 0);
      $cs_cups['squad2_id'] = (isset($match['squad_id2']) ? $match['squad_id2'] : 0);
      $cs_cups['cupmatches_loserbracket'] = $match['loserbracket'];
      $cs_cups['cupmatches_round'] = $match['round'];
      $cs_cups['cupmatches_tree_order'] = $match['tree_order'];
      $cs_cups['cupmatches_match'] = $matchnr;
      $cs_cups['cupmatches_nextmatch'] = $match['nextmatch'];
      $cs_cups['cupmatches_nextmatchlb'] = $match['nextmatchlb'];
      $cs_cups['cupmatches_seed1'] = $match['seed1'];
      $cs_cups['cupmatches_seed2'] = $match['seed2'];
      $cells = array_keys($cs_cups);
      $values = array_values($cs_cups);
      cs_sql_insert(__FILE__,'cupmatches',$cells,$values);
  	}
		/* autoclose the matches */
		cs_cups_autoclose($id);
  }
  cs_redirect($cs_lang['started_successfully'],'cups','manage');
  
} else {

  $id = (int) $_GET['id'];
  
  $cupsel = cs_sql_select(__FILE__,'cups','cups_teams, cups_system','cups_id = ' . $id);

  if ($cupsel['cups_system'] == CS_CUPS_TYPE_TEAMS) {
    // remove squads automatically which doesn't exist anymore in the database
    $del = cs_sql_select(__FILE__,'cupsquads cq LEFT JOIN {pre}_squads sq ON cq.squads_id = sq.squads_id','cq.squads_id','sq.squads_id IS NULL AND cups_id = ' . $id,0,0,0);
    if (!empty($del))
      foreach($del as $del_id)
        cs_sql_delete(__FILE__,'cupsquads', $del_id['squads_id'], 'squads_id');
  }
  
  $squads_count = cs_sql_count(__FILE__,'cupsquads','cupsquads_checkedin = 1 AND cups_id = ' . $id);
  
  if (($cupsel['cups_teams'] / 2) >= $squads_count) {
    
    $bin = decbin($squads_count);
    if (substr_count($bin,'1') != 1) {
      // Get the smallest potency of 2 bigger then the team count
      $new = '1';
      for ($x = 0; $x < strlen($bin); $x++) {
        $new .= '0';
      }
      settype($new,'integer');
      $new = bindec($new);
    } else {
      // If the team count is a potency of 2 already
      $new = $squads_count == 1 ? 2 : $squads_count;
    }
    
    $data = array();
    $data['lang']['reduce'] = $cs_lang['more_teams_required'] . $cs_lang['reduce_1'] . $new . $cs_lang['reduce_2'];
    $data['var']['cups_id'] = $id;
    $data['var']['teams'] = $new;
    
    echo cs_subtemplate(__FILE__, $data, 'cups', 'start_reduce');
    
  } else {
    
    $data = array();
    $data['cup']['id'] = $id;
    
    echo cs_subtemplate(__FILE__, $data, 'cups', 'start');
    
  }  
}
