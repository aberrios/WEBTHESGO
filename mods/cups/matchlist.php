<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include_once 'mods/cups/functions.php';

$cups_id = !empty($_POST['where']) ? (int) $_POST['where'] : (int) $_GET['where'];
$lb = !empty($_POST['lb']) ? 1 : (!empty($_GET['lb']) ? 1 : 0);

$maxteams = cs_sql_select(__FILE__,'cups','cups_teams','cups_id = '.$cups_id);
$maxrounds = cs_cups_log($maxteams['cups_teams']);

$round = !empty($_POST['round']) ? (int) $_POST['round'] : (!empty($_GET['round']) ? (int) $_GET['round'] : 1);

$start = empty($_GET['start']) ? 0 : (int) $_GET['start'];

$system = cs_sql_select(__FILE__,'cups','cups_system, cups_brackets, cups_access','cups_id = ' . $cups_id);

if ($account['access_cups'] < $system['cups_access'] || $system['cups_access'] == 0)
{
	echo $cs_lang['access_denied'];
	return;
}

if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS) {
  $cs_sort[1] = 'sq1.squads_name ASC';
  $cs_sort[2] = 'sq1.squads_name DESC';
  $cs_sort[3] = 'sq2.squads_name ASC';
  $cs_sort[4] = 'sq2.squads_name DESC';
} else {
  $cs_sort[1] = 'usr1.users_nick ASC';
  $cs_sort[2] = 'usr1.users_nick DESC';
  $cs_sort[3] = 'usr2.users_nick ASC';
  $cs_sort[4] = 'usr2.users_nick DESC';
}
$cs_sort[5] = 'cm.cupmatches_loserbracket ASC, cm.cupmatches_match ASC';
$cs_sort[6] = 'cm.cupmatches_loserbracket DESC, cm.cupmatches_match ASC';

$sort = empty($_GET['sort']) ? 5 : (int) $_GET['sort'];
$order = $cs_sort[$sort];

$tables  = 'cupmatches cm';
if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS) {
  $tables .= ' LEFT JOIN {pre}_squads sq1 ON cm.squad1_id = sq1.squads_id';
  $tables .= ' LEFT JOIN {pre}_squads sq2 ON cm.squad2_id = sq2.squads_id';
} else {
  $tables .= ' LEFT JOIN {pre}_users usr1 ON cm.squad1_id = usr1.users_id';
  $tables .= ' LEFT JOIN {pre}_users usr2 ON cm.squad2_id = usr2.users_id';
}
$tables .= ' LEFT JOIN {pre}_cupsquads cs1 ON cm.squad1_id = cs1.squads_id AND cm.cups_id = cs1.cups_id';
$tables .= ' LEFT JOIN {pre}_cupsquads cs2 ON cm.squad2_id = cs2.squads_id AND cm.cups_id = cs2.cups_id';

$cells  = 'cm.cupmatches_id AS cupmatches_id, cm.cupmatches_score1 AS cupmatches_score1, ';
$cells .= 'cm.cupmatches_score2 AS cupmatches_score2, cm.cupmatches_accepted1 AS cupmatches_accepted1, ';
$cells .= 'cm.cupmatches_accepted2 AS cupmatches_accepted2, cm.cupmatches_winner AS cupmatches_winner, ';
$cells .= 'cs1.cupsquads_seed AS seed1, cs1.cupsquads_autoseed AS autoseed1, ';
$cells .= 'cs2.cupsquads_seed AS seed2, cs2.cupsquads_autoseed AS autoseed2, ';

if ($system['cups_brackets'] == CS_CUPS_SYSTEM_LB)
  $cells .= 'cm.cupmatches_loserbracket AS cupmatches_loserbracket, ';
if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS) {
  $cells .= 'cm.squad1_id AS squad1_id, sq1.squads_name AS squad1_name, ';
  $cells .= 'cm.squad2_id AS squad2_id, sq2.squads_name AS squad2_name';
} else {
  $cells .= 'cm.squad1_id AS squad1_id, usr1.users_nick AS user1_nick, ';
  $cells .= 'cm.squad2_id AS squad2_id, usr2.users_nick AS user2_nick, ';
  $cells .= 'usr1.users_active AS user1_active, usr1.users_delete AS user1_delete, ';
  $cells .= 'usr2.users_active AS user2_active, usr2.users_delete AS user2_delete';
}

$data = array();


switch ($system['cups_brackets'])
{
default:
case CS_CUPS_SYSTEM_KO: // KO
	if ($round <= 0 || $round > $maxrounds)
		$round = 1;
	break;
case CS_CUPS_SYSTEM_KO3RD: // KO 3rd place
case CS_CUPS_SYSTEM_LB: // LB Grand Final
	if ($lb == 1)
	{
		if ($round < 1 || $round / 2.0 > $maxrounds - 0.5)
			$round = 1;
	}
	else
	{
		if ($round <= 0 || $round > $maxrounds + 1)
			$round = 1;
		else if ($round == $maxrounds + 1)
			$round = 0;
	}
	break;
}

$cond = 'cm.cupmatches_round = \''.$round.'\' AND cm.cups_id = \''.$cups_id.'\' AND cm.cupmatches_loserbracket = '.$lb;

$data['matches'] = cs_sql_select(__FILE__,$tables,$cells,$cond,$order,0,0);
$data['cups']['id'] = $cups_id;

$data['rounds'] = array();
$max = $maxrounds;

for ($i = 0; $i < $max; $i++) {
  $j = $i+1;
  $data['rounds'][$i]['value'] = $j;
  $data['rounds'][$i]['if']['notselected'] = ($lb == 0 && $data['rounds'][$i]['value'] == $round ? false : true);
  $data['rounds'][$i]['name'] = ($j == $maxrounds) ? $cs_lang['final'] : $cs_lang['round'].' '.$j; 
}
switch ($system['cups_brackets'])
{
default:
case CS_CUPS_SYSTEM_KO: // KO
	$data['if']['haslbround'] = false;
	break;
case CS_CUPS_SYSTEM_KO3RD: // KO 3rd place
	$data['if']['haslbround'] = false;
  $data['rounds'][$max]['value'] = $maxrounds + 1;
  $data['rounds'][$max]['if']['notselected'] = 0 == $round ? false : true;
  $data['rounds'][$max]['name'] = $cs_lang['third_place']; 
	break;
case CS_CUPS_SYSTEM_LB: // LB Grand Final
	$data['lbrounds'] = array();
	$data['if']['haslbround'] = true;
  $data['rounds'][$max]['value'] = $maxrounds + 1;
  $data['rounds'][$max]['if']['notselected'] = 0 == $round ? false : true;
  $data['rounds'][$max]['name'] = $cs_lang['grand_final']; 
  $i = 1.0;
  $run = 0;
	while ($i < $maxrounds) {
	  $j = (int) (2*$i);
	  $data['lbrounds'][$run]['value'] = $j;
	  $data['lbrounds'][$run]['if']['lbnotselected'] = ($lb == 1 && $data['lbrounds'][$run]['value'] == $round ? false : true);
	  $data['lbrounds'][$run]['name'] = $cs_lang['round'].' '.($j % 2 == 0 ? sprintf('%1.0F', $i) : sprintf('%1.1F', $i));
	  $i += 0.5; 
	  $run++;
	}
	break;
}

$data['vars']['matchcount'] = count($data['matches']);
$data['vars']['cups_id'] = $cups_id;
$data['pages']['list'] = cs_pages('cups','matchlist', $data['vars']['matchcount'], $start, $cups_id, $sort);
$data['if']['brackets'] = $system['cups_brackets'] == CS_CUPS_SYSTEM_LB ? true : false;
$data['sort']['team1'] = cs_sort('cups','matchlist', $start, $cups_id, 1, $sort, 'round=' . $round);
$data['sort']['team2'] = cs_sort('cups','matchlist', $start, $cups_id, 3, $sort, 'round=' . $round);
$data['sort']['bracket'] = cs_sort('cups','matchlist', $start, $cups_id, 5, $sort, 'round=' . $round);
if ($system['cups_system'] == CS_CUPS_TYPE_USERS) $data['lang']['team'] = $cs_lang['player'];

$notallowed = array(CS_CUPS_TEAM_UNKNOWN, CS_CUPS_TEAM_BYE);
for ($i = 0; $i < $data['vars']['matchcount']; $i++) {
  
	$data['matches'][$i]['betlink'] = '';
	if (!empty($account['access_bets']) && $account['access_bets'] > 3)
	{
		$cupmatch = $data['matches'][$i];
    if ($cupmatch['cupmatches_winner'] == CS_CUPS_TEAM_UNKNOWN
        && !in_array($cupmatch['squad1_id'], $notallowed) && !in_array($cupmatch['squad2_id'], $notallowed)
        && empty($cupmatch['cupmatches_accepted1']) && empty($cupmatch['cupmatches_accepted2'])
        && empty($cupmatch['cupmatches_score1']) && empty($cupmatch['cupmatches_score2']))
			$data['matches'][$i]['betlink'] = cs_link(cs_icon('bets'), 'bets','create','cupmatchid='.$data['matches'][$i]['cupmatches_id']);
	}
  if ($system['cups_brackets'] == CS_CUPS_SYSTEM_LB)
    $data['matches'][$i]['bracket'] = empty($data['matches'][$i]['cupmatches_loserbracket']) ? $cs_lang['winners'] : $cs_lang['losers'];
  
  $data['matches'][$i]['status'] = empty($data['matches'][$i]['cupmatches_accepted1']) || empty($data['matches'][$i]['cupmatches_accepted2']) ?
    $cs_lang['open'] : $cs_lang['closed'];
  
	switch ($data['matches'][$i]['squad1_id'])
	{
	case CS_CUPS_TEAM_UNKNOWN: // not determined yet
		$data['matches'][$i]['team1'] = $cs_lang['unknown'];
		break;
	case CS_CUPS_TEAM_BYE: // free win
		$data['matches'][$i]['team1'] = $cs_lang['bye'];
		break;
	default:
  	if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS)
   			$data['matches'][$i]['team1'] = (empty($data['matches'][$i]['squad1_name']) AND !empty($data['matches'][$i]['squad1_id'])) ? '? ID:'.$data['matches'][$i]['squad1_id'] : cs_link(cs_secure($data['matches'][$i]['squad1_name']),'squads','view','id='.$data['matches'][$i]['squad1_id']);
		else
   			$data['matches'][$i]['team1'] = cs_user($data['matches'][$i]['squad1_id'],$data['matches'][$i]['user1_nick'], $data['matches'][$i]['user1_active'], $data['matches'][$i]['user1_delete']);
    if ($data['matches'][$i]['autoseed1'] == 0)
    	$data['matches'][$i]['team1'] .= ' (#'.$data['matches'][$i]['seed1'].')';
		break;
	}
	switch ($data['matches'][$i]['squad2_id'])
	{
	case CS_CUPS_TEAM_UNKNOWN: // not determined yet
		$data['matches'][$i]['team2'] = $cs_lang['unknown'];
		break;
	case CS_CUPS_TEAM_BYE: // free win
		$data['matches'][$i]['team2'] = $cs_lang['bye'];
		break;
	default:
  	if ($system['cups_system'] == CS_CUPS_TYPE_TEAMS)
    	$data['matches'][$i]['team2'] = (empty($data['matches'][$i]['squad2_name']) AND !empty($data['matches'][$i]['squad2_id'])) ? '? ID:'.$data['matches'][$i]['squad2_id'] : cs_link(cs_secure($data['matches'][$i]['squad2_name']),'squads','view','id='.$data['matches'][$i]['squad2_id']);
		else
    	$data['matches'][$i]['team2'] = cs_user($data['matches'][$i]['squad2_id'],$data['matches'][$i]['user2_nick'], $data['matches'][$i]['user2_active'], $data['matches'][$i]['user2_delete']);
    if ($data['matches'][$i]['autoseed2'] == 0)
    	$data['matches'][$i]['team2'] .= ' (#'.$data['matches'][$i]['seed2'].')';
    break;
	}
}

echo cs_subtemplate(__FILE__, $data, 'cups', 'matchlist');
