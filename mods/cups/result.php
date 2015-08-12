<?php
// ClanSphere 2011
// 

$cs_lang = cs_translate('cups');

include_once 'mods/cups/functions.php';

$cups_id = (int) $_GET['id'];

$cup = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = ' . $cups_id);

if ($account['access_cups'] < $cup['cups_access'] || $cup['cups_access'] == 0)
{
        echo $cs_lang['access_denied'];
        return;
}

$key = 'lang='.$account['users_lang'].'&cup='.$cups_id.'&access='.$account['access_cups'];
if (function_exists('cs_datacache_load'))
        $cachedata = cs_datacache_load('cups', 'result', $key, false);
else
        $cachedata = false;
if ($cachedata !== false)
{
        echo $cachedata;
        return;
}

$gridsize = $cup['cups_teams'];
$rounds = cs_cups_log($gridsize);
$cpos = $gridsize;
$result = array();

/* get the teams */
$teams = cs_cups_get_teams($cups_id, $cup['cups_system'], $cs_lang);
$thirdplace = null;

switch ($cup['cups_brackets'])
{
case CS_CUPS_SYSTEM_KO3RD:
	/* get the 3rd place match */
	$rwhere = 'cups_id = '.$cups_id.' AND cupmatches_accepted1 = 1 AND cupmatches_accepted2 = 1 AND cupmatches_round = 0';
	$thirdplace = cs_sql_select(__FILE__, 'cupmatches', '*', $rwhere, 0, 0, 1);
	/* fallthrough, almost the same as KO */
case CS_CUPS_SYSTEM_KO:
	/* the position of the team who loses in a KO round is always (number of players / 2) + 1  */
	$pos2 = floor($gridsize / 2);
	$pos = $pos2 + 1;
	for ($i = 1; $i <= $rounds; $i++)
	{
		/* only find finished games */
		$rwhere = 'cups_id = '.$cups_id.' AND cupmatches_accepted1 = 1 AND cupmatches_accepted2 = 1 AND cupmatches_round = '.$i;
		$playedgames = cs_sql_select(__FILE__, 'cupmatches', '*', $rwhere, 0, 0, 0);
		if (count($playedgames))
		foreach ($playedgames as $game)
		{
			/* the loser is the one we seek */
			if ($game['squad1_id'] != $game['cupmatches_winner'])
				$result[$cpos--] = array($pos, intval($game['squad1_id']));
			else
				$result[$cpos--] = array($pos, intval($game['squad2_id']));
			/* if we are in the semi-final and we have a 3rd place match, we need to set pos 4 */
			if ($cup['cups_brackets'] == CS_CUPS_SYSTEM_KO3RD && $i + 1 == $rounds && is_array($thirdplace))
			{
				if (in_array($result[$cpos+1][1], array($thirdplace['squad1_id'], $thirdplace['squad2_id'])))
				{
					if ($result[$cpos+1][1] != $thirdplace['cupmatches_winner'])
					{
						$result[$cpos+1][0] = 4;
					}
				}
			}
		}
		/* last round should have only 1 match (if any finished) */
		if ($i == $rounds && count($playedgames) == 1)
		{
			if ($playedgames[0]['squad1_id'] == $playedgames[0]['cupmatches_winner'])
				$result[$cpos--] = array($pos-1, intval($game['squad1_id']));
			else
				$result[$cpos--] = array($pos-1, intval($game['squad2_id']));
		}
		/* get higher positions for next round */
		$pos2 = floor($pos2 / 2);
		$pos = $pos2 + 1;
	}
	if ($cpos != 0)
	{
		/* we still have unfinished games */
	}
	break;
case CS_CUPS_SYSTEM_LB:
	/* get the grandfinal match */
	$rwhere = 'cups_id = '.$cups_id.' AND cupmatches_accepted1 = 1 AND cupmatches_accepted2 = 1 AND cupmatches_round = 0';
	$grandfinal = cs_sql_select(__FILE__, 'cupmatches', '*', $rwhere, 0, 0, 1);
	
	/* we are only interested in the loser bracket rounds, since all players except
	 * the two players from the grand final will end here
	 */
	$pos2 = floor($gridsize / 2);
	$pos = floor($pos2 * 1.5) + 1;
	$even = false;
	for ($i = 1; floor($i) < $rounds; $i += 0.5)
	{
		/* only find finished games */
		$rwhere = 'cups_id = '.$cups_id.' AND cupmatches_loserbracket = 1 AND cupmatches_accepted1 = 1 AND cupmatches_accepted2 = 1 AND cupmatches_round = '.floor($i*2);
		$playedgames = cs_sql_select(__FILE__, 'cupmatches', '*', $rwhere, 0, 0, 0);
		if (count($playedgames))
		foreach ($playedgames as $game)
		{
			/* the loser is the one we seek */
			if ($game['squad1_id'] != $game['cupmatches_winner'])
				$result[$cpos--] = array($pos, intval($game['squad1_id']));
			else
				$result[$cpos--] = array($pos, intval($game['squad2_id']));
			/* if we are in the semi-final and we have a 3rd place match, we need to set pos 4 */
		}
		if ($even)
		{
			$pos2 = floor($pos2 / 2);
			$pos = floor($pos2 * 1.5) + 1;	
		}
		else
		{
			$pos = floor($pos2) + 1;	
		}
		$even = !$even;
	}
	/* get number 1 and 2 */
	if (is_array($grandfinal))
	{
		if ($grandfinal['squad1_id'] != $grandfinal['cupmatches_winner'])
		{
			$result[$cpos--] = array(2, intval($grandfinal['squad1_id']));
			$result[$cpos--] = array(1, intval($grandfinal['squad2_id']));
		}
		else
		{
			$result[$cpos--] = array(2, intval($grandfinal['squad2_id']));
			$result[$cpos--] = array(1, intval($grandfinal['squad1_id']));
		}
	}
	if ($cpos != 0)
	{
		/* we still have unfinished games */
	}
	break;
}

/* reset the byes to the lowest positions */
$prevpl = $gridsize+1;
for ($i = $gridsize; $i > 0; $i--)
{
	if (isset($result[$i]))
	{
		if ($result[$i][1] == CS_CUPS_TEAM_BYE)
		{
			if ($prevpl <= $gridsize && $result[$i][0] == $result[$prevpl][0])
			{
				/* replace prevpl with empty slot everything */
				$result[$i][1] = $result[$prevpl][1];
				$result[$prevpl][1] = CS_CUPS_TEAM_BYE;
				$prevpl = $gridsize+1;
				$i = $prevpl;
			}
		}
		else if ($prevpl > $gridsize)
		{
			$prevpl = $i;
		}
	}
}


/* create the real standings */
$real = array();

for ($i = 1; $i <= $gridsize; $i++)
{
	$real[$i] = array('pos' => $i, 'id' => CS_CUPS_TEAM_UNKNOWN);
}
/* just fill up the spots */
for ($i = 1; $i <= $gridsize; $i++) 
{
	if (!is_array($result[$i]))
		continue;
	$pos = $result[$i][0];
	while ($real[$pos]['id'] != CS_CUPS_TEAM_UNKNOWN && $pos <= $gridsize)
	{
		$pos++;
	}
	if ($real[$pos]['id'] == CS_CUPS_TEAM_UNKNOWN)
	{
		$real[$pos]['pos'] = $result[$i][0];
		$real[$pos]['id'] = $result[$i][1];
	}
}

/* fill the standings with real data */
$prevpos = -1;
$data['image']['gold'] = cs_html_img('symbols/awards/pokal_gold.png');
$data['image']['silver'] = cs_html_img('symbols/awards/pokal_silber.png');
$data['image']['bronze'] = cs_html_img('symbols/awards/pokal_bronze.png');
$data['result'] = array();
foreach ($real as $key => $info)
{
	switch ($real[$key]['pos'])
	{
	case 1:
		$real[$key]['img'] = $data['image']['gold'];
		break;
	case 2:
		$real[$key]['img'] = $data['image']['silver'];
		break;
	case 3:
		$real[$key]['img'] = $data['image']['bronze'];
		break;
	default:
		$real[$key]['img'] = '';
		break;
	}
	
	if ($prevpos == $real[$key]['pos'])
		$real[$key]['pos'] = '';
	else
		$prevpos = $real[$key]['pos'];

	 
	switch ($real[$key]['id'])
	{
	case CS_CUPS_TEAM_UNKNOWN:
		$real[$key]['link'] = $cs_lang['unknown'];
		break;
	case CS_CUPS_TEAM_BYE:
		$real[$key]['link'] = $cs_lang['bye'];
		break;
	default:
		foreach ($teams as $tkey => $tinfo)
		{
			if ($tinfo['squads_id'] == $real[$key]['id'])
			{
				$real[$key]['link'] = $tinfo['link'];
				unset($teams[$tkey]);
			}
		}
		break;
	}
	$data['result'][] = $real[$key];
}

$cache = cs_subtemplate(__FILE__, $data, 'cups', 'result');

if (function_exists('cs_datacache_load'))
	cs_datacache_create('cups', 'result', $key, $cache, 0);

echo $cache;
?>
