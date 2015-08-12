<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include_once('mods/cups/functions.php');

$cups_id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : 0);


$data['seed']['error'] = '';
$cups = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = '.$cups_id, 0, 0, 1);
if (empty($cups['cups_id']))
{
	$data['if']['no_teams'] = true;
  $data['seed']['message'] = $cs_lang['no_data'];
  
	echo cs_subtemplate(__FILE__, $data, 'cups', 'seed');
	return;
}

$matchcount = cs_sql_count(__FILE__,'cupmatches', 'cups_id = ' . $cups_id);
if (!empty($matchcount))
{
	$data['if']['no_teams'] = true;
  $data['seed']['message'] = $cs_lang['cup_started'];
  
	echo cs_subtemplate(__FILE__, $data, 'cups', 'seed');
	return;
}

$teams = cs_cups_get_teams($cups['cups_id'], $cups['cups_system'], $cs_lang);
if (empty($teams))
{
	$data['if']['no_teams'] = true;
  $data['seed']['message'] = $cs_lang['no_data'];
  
	echo cs_subtemplate(__FILE__, $data, 'cups', 'seed');
	return;
}

if (!empty($_POST['submit']) || !empty($_POST['reseed']))
{
	$has_errors = false;
	$seedinfo = array();
	$seedvals = array();
	foreach ($teams as $key => $team)
	{
		if (!isset($_POST['seed_'.$team['cupsquads_id']]))
		{
			$has_errors = true;
			continue;
		}
		$seed = (int) $_POST['seed_'.$team['cupsquads_id']];
		if (empty($_POST['autoseed_'.$team['cupsquads_id']]) && ($seed <= 0 || $seed > CS_CUPS_MAX_SEED))
		{
			$has_errors = true;
			$seed = CS_CUPS_MAX_SEED + 1;
		}
		$autoseed = 0;
		if ($seed == CS_CUPS_MAX_SEED + 1 || !empty($_POST['autoseed_'.$team['cupsquads_id']]))
		{
			$seed = CS_CUPS_MAX_SEED + 1;
			$autoseed = 1;
		}
		if ($autoseed == 0)
		{
			if (in_array($seed, $seedvals))
			{
				$has_errors = true;
			}
			else
				$seedvals[] = $seed;
		}
		$seedinfo[] = array('cupsquads_id' => $team['cupsquads_id'],
												'cupsquads_seed' => $seed,
												'cupsquads_autoseed' => $autoseed);
		$teams[$key]['cupsquads_seed'] = $seed;
		if (empty($autoseed))
 		{
   		$teams[$key]['seed_text'] = $seed;
   		$teams[$key]['autoseed_on'] = '';
   		$teams[$key]['autoseed_off'] = 'checked';
   	}
  	else
   	{
   		$teams[$key]['seed_text'] = $cs_lang['auto'];
   		$teams[$key]['autoseed_on'] = 'checked';
   		$teams[$key]['autoseed_off'] = '';
   	}
	}
	if (!$has_errors)
	{
		foreach ($seedinfo as $seeded)
		{
			$cells = array('cupsquads_seed', 'cupsquads_autoseed');
			$values = array($seeded['cupsquads_seed'], $seeded['cupsquads_autoseed']);
			cs_sql_update(__FILE__, 'cupsquads', $cells, $values, $seeded['cupsquads_id']);
		}
		$data['if']['no_teams'] = true;
  	$data['seed']['message'] = $cs_lang['changes_done'];
	  
		if (!empty($_POST['reseed']))
		{
			cs_cups_reseed($cups['cups_id']);
		}
		echo cs_subtemplate(__FILE__, $data, 'cups', 'seed');
		return;
	}
	else
	{
  	$data['seed']['error'] = $cs_lang['error'];
	}
}

$data['if']['no_teams'] = false;

$data['cups'] = $cups;
$data['teams'] = array();
$run = 0;
foreach ($teams as $team)
{
	if ($team['cupsquads_seed'] == 10000)
		$team['cupsquads_seed'] = 0;
	$data['teams'][$run++] = $team;
}
echo cs_subtemplate(__FILE__, $data, 'cups', 'seed');
?>
