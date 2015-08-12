<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

/* Cup and grid image generation code:
 *
 * Copyright (c) 2010, Wetzels Holding BV, Remy Wetzels <mindcrime@gab-clan.org>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of Wetzels Holding BV nor the names of its
 *   contributors may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Special notice by Remy Wetzels <mindcrime@gab-clan.org>, September 14, 2010:
 * Permission is hereby granted by Wetzels Holding BV to the ClanSphere Project
 * to omit the above disclaimer in their general documentation and/or
 * ClanSphere about section of the code.
 */

$cs_lang = cs_translate('cups');
$cs_option = cs_sql_option(__FILE__, 'cups');
$datahtml = array();
$datahtml['tree'] = array();

include_once 'mods/cups/functions.php';

$cups_id = (int) $_GET['id'];
/* tree.php might set the variable below */
$gridonly = (isset($gridonly) && $gridonly == true) || !empty($_GET['gridonly']) ? true : false;

$cup = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = ' . $cups_id);
$rounds = cs_cups_log($cup['cups_teams']);
$rounds_1 = (float) $rounds - 0.5;

if ($account['access_cups'] < $cup['cups_access'] || $cup['cups_access'] == 0 || $cup['cups_brackets'] != CS_CUPS_SYSTEM_LB)
{
	echo $cs_lang['access_denied'];
  return;
}

if (defined('IN_TREE'))
	$tree = true;
else
	$tree = false;
	
$width = empty($cs_option['width']) ? (empty($_GET['width']) ? 600 : (int) $_GET['width']) : $cs_option['width'];
$key = 'lang='.$account['users_lang'].'&cup='.$cups_id.'&lb=1&gridonly='.($gridonly ? 1 : 0).'&tree='.($tree ? 1 : 0).'&width='.$width.'&access='.$account['access_cups'];
if (function_exists('cs_datacache_load'))
	$cachedata = cs_datacache_load('cups', 'viewtree', $key, false);
else
	$cachedata = false;
if ($cachedata !== false)
{
	echo $cachedata;
	return;
}

$tables = 'cupmatches cm LEFT JOIN ';
$tables .= $cup['cups_system'] == CS_CUPS_TYPE_USERS ? '{pre}_users u1 ON u1.users_id = cm.squad1_id LEFT JOIN {pre}_users u2 ON u2.users_id = cm.squad2_id' :
  '{pre}_squads sq1 ON sq1.squads_id = cm.squad1_id LEFT JOIN {pre}_squads sq2 ON sq2.squads_id = cm.squad2_id';
$tables .= ' LEFT JOIN {pre}_cupsquads cs1 ON cm.squad1_id = cs1.squads_id AND cs1.cups_id = cm.cups_id LEFT JOIN {pre}_cupsquads cs2 ON cm.squad2_id = cs2.squads_id AND cs2.cups_id = cm.cups_id';
$cells = $cup['cups_system'] == CS_CUPS_TYPE_USERS
  ? 'u1.users_nick AS team1_name, cm.squad1_id AS team1_id, u2.users_nick AS team2_name, cm.squad2_id AS team2_id'
  : 'sq1.squads_name AS team1_name, cm.squad1_id AS team1_id, sq2.squads_name AS team2_name, cm.squad2_id AS team2_id';
$cells .= ', cm.cupmatches_winner AS cupmatches_winner, cm.cupmatches_accepted1 AS cupmatches_accepted1';
$cells .= ', cm.cupmatches_accepted2 AS cupmatches_accepted2, cm.cupmatches_tree_order AS cupmatches_tree_order';
$cells .= ', cs1.cupsquads_seed AS seed1, cs1.cupsquads_autoseed AS autoseed1';
$cells .= ', cs2.cupsquads_seed AS seed2, cs2.cupsquads_autoseed AS autoseed2';
$cells .= ', cm.cupmatches_seed1 AS cupmatches_seed1, cm.cupmatches_seed2 AS cupmatches_seed2';
$cells .= ', cm.cupmatches_score1 AS cupmatches_score1, cm.cupmatches_score2 AS cupmatches_score2';
$cells .= ', cm.cupmatches_nextmatch AS cupmatches_nextmatch, cm.cupmatches_match AS cupmatches_match';
$cells .= ', cm.cupmatches_id AS cupmatches_id';

$where = 'cm.cups_id = ' . $cups_id . ' AND cm.cupmatches_loserbracket = 1 AND cm.cupmatches_round = ';

$cupmatches = array();
$run = 0;
$max_tree_order = 0;
$max_matches = 0;
for ($i=1.0; $i <= $rounds_1; $i += 0.5) {
  $temp = cs_sql_select(__FILE__, $tables, $cells, $where . (int) ($i * 2), 'cm.cupmatches_tree_order ASC',0,0);
  if (count($temp))
  foreach ($temp as $match)
  {
  	$match['team1_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team1_id'], $match['team1_name'], $match['autoseed1'], $match['seed1'], true, true);
  	$match['team2_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team2_id'], $match['team2_name'], $match['autoseed2'], $match['seed2'], true, true);
    $cupmatches[$run][$match['cupmatches_tree_order']] = $match;
		if ($match['cupmatches_tree_order'] > $max_tree_order)
			$max_tree_order = $match['cupmatches_tree_order'];
  }
	if (isset($cupmatches[$run]) && count($cupmatches[$run]) > $max_matches)
		$max_matches = count($cupmatches[$run]);
	$run++;
}

if (count($cupmatches) == 0)
{
	echo $cs_lang['no_matches'];
	return;
}

$max_grid = ($max_tree_order*4) / $max_matches;
$max_pos = ($max_grid % 2 == 0) ? $max_grid + 2 : $max_grid + 1;

// Calc-Defs
$count_matches = $max_pos;
$maxrounds = $rounds;
$rounds = (int) ($rounds_1 * 2) - 1;
$lb = ' (LB)';
include 'mods/cups/viewtree_head.php';
$rounds = $maxrounds;


$count_cupmatches = 0;
$result = $cup['cups_teams'];
while ($result > 2) { $result /= 2; $count_cupmatches += $result; }
$count_cupmatches += 2;
$round = 0;
$run = 0;
$match_run = 0;

$run = 0;
$prevmatches = array();
/* the matrix (row, column) */
$matrix = array();
$maxrows = 0;
for ($i=1.0; $i <= $rounds_1; $i += 0.5) {
	$column = (($c+1) * $i*2) - ($c+1 - 1);
	foreach ($cupmatches[$run] as $cupmatch)
	{
		if ($cupmatch['cupmatches_nextmatch'] != CS_CUPS_NO_NEXTMATCH)
		{
			/* get the tree order of the lowest any previous matches so that we can draw a line */
			if (!isset($prevmatches[$cupmatch['cupmatches_nextmatch']]))
			{
				$prevmatches[$cupmatch['cupmatches_nextmatch']] = array();
				$prevmatches[$cupmatch['cupmatches_nextmatch']][0] = array($cupmatch['cupmatches_match'], $cupmatch['cupmatches_tree_order']);
			}
			else if ($prevmatches[$cupmatch['cupmatches_nextmatch']][0][1] > $cupmatch['cupmatches_tree_order'])
			{
				$prevmatches[$cupmatch['cupmatches_nextmatch']][1] = $prevmatches[$cupmatch['cupmatches_nextmatch']][0]; 
				$prevmatches[$cupmatch['cupmatches_nextmatch']][0] = array($cupmatch['cupmatches_match'], $cupmatch['cupmatches_tree_order']);
			}
			else
				$prevmatches[$cupmatch['cupmatches_nextmatch']][1] = array($cupmatch['cupmatches_match'], $cupmatch['cupmatches_tree_order']);
		}
		$gridpos = floor(($cupmatch['cupmatches_tree_order']*4) / $max_matches);
  	$currow = $gridpos;
  
		if ($i > 1.0)
		{
			if (count($prevmatches[$cupmatch['cupmatches_match']]) == 1)
			{
				$pmatch1 = $prevmatches[$cupmatch['cupmatches_match']][0];
				$prevrow1 = floor(($pmatch1[1]*4.0) / $max_matches);

				if ($prevrow1 <> $currow)
				{
					if ($prevrow1 > $currow)
					{
						/* should not happen? */
						$matrix[$column-2][$prevrow1] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-2][$prevrow1+1] = array('class' => 'cup-grid-vertical-split-right');
						$matrix[$column-2][$prevrow1+2] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-1][$prevrow1+1] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-1][$currow+2] = array('class' => 'cup-grid-angle-up-right');
						for ($j = $currow + 2; $j < $prevrow1; $j++)
							$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical');
					}
					else
					{
						$matrix[$column-2][$prevrow1] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-2][$prevrow1+1] = array('class' => 'cup-grid-vertical-split-right');
						$matrix[$column-2][$prevrow1+2] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-1][$prevrow1+1] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-1][$currow+1] = array('class' => 'cup-grid-angle-down-right');
						for ($j = $prevrow1 + 2; $j < $currow; $j++)
							$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical');
					}
				}
				else
					$matrix[$column-1][$currow] = array('class' => 'cup-grid-horizontal');
			}
			else if (count($prevmatches[$cupmatch['cupmatches_match']]) == 2)
			{
				$pmatch1 = $prevmatches[$cupmatch['cupmatches_match']][0];
				$pmatch2 = $prevmatches[$cupmatch['cupmatches_match']][1];

				$prevrow1 = floor (($pmatch1[1]*4.0) / $max_matches);
				$prevrow2 = floor (($pmatch2[1]*4.0) / $max_matches);

				if ($prevrow1 < $prevrow2)
				{
						$matrix[$column-2][$prevrow1] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-2][$prevrow1+1] = array('class' => 'cup-grid-vertical-split-right');
						$matrix[$column-2][$prevrow1+2] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-2][$prevrow2] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-2][$prevrow2+1] = array('class' => 'cup-grid-vertical-split-right');
						$matrix[$column-2][$prevrow2+2] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-1][$prevrow1+1] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-1][$prevrow2+1] = array('class' => 'cup-grid-angle-up');
						for ($j = $prevrow1 + 1; $j < $prevrow2; $j++)
						{
								if ($j == $currow - 1)
									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-angle-down-right');
								else if ($j == $currow + 1)
									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-angle-up-right');
								else if ($j != $currow)
									/* draw vertical line */
									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical');
//								if ($j == $currow)
//									/* draw to this one */
//									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical-split-right');
//								else
//									/* draw vertical line */
//									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical');
						}
				}
				else
				{
						$matrix[$column-2][$prevrow1] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-2][$prevrow1+1] = array('class' => 'cup-grid-vertical-split-right');
						$matrix[$column-2][$prevrow1+2] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-2][$prevrow2] = array('class' => 'cup-grid-angle-down');
						$matrix[$column-2][$prevrow2+1] = array('class' => 'cup-grid-vertical-split-right');
						$matrix[$column-2][$prevrow2+2] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-1][$prevrow1+1] = array('class' => 'cup-grid-angle-up');
						$matrix[$column-1][$prevrow2+1] = array('class' => 'cup-grid-angle-down');
						for ($j = $prevrow2 + 1; $j < $prevrow1; $j++)
						{
								if ($j == $currow - 1)
									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-angle-down-right');
								else if ($j == $currow + 1)
									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-angle-up-right');
								else if ($j != $currow)
									/* draw vertical line */
									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical');
//								if ($j == $currow)
//									/* draw to this one */
//									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical-split-right');
//								else
//									/* draw vertical line */
//									$matrix[$column-1][$j+1] = array('class' => 'cup-grid-vertical');
						}
				}
			}
		}
		if ($i - floor($i) == 0.0)
		{
			if ($cs_option['scores'] == 1)
				$matrix[$column+1][$currow] = array('class' => 'cup-grid-score');
			$matrix[$column][$currow] = array('class' => 'cup-grid-team');
		}
		else
		{
			if ($cs_option['scores'] == 1)
				$matrix[$column+1][$currow] = array('class' => 'cup-grid-score-lb');
			$matrix[$column][$currow] = array('class' => 'cup-grid-team-lb');
		}
		
		$string = '';
		if (!empty($cupmatch['team1_name']))
   		$string = $cupmatch['team1_name'];
//		else
//   		$string = '#'.$cupmatch['cupmatches_seed1'];
  	if (!empty($string))
  	{
			$matrix[$column][$currow]['content'] = $string; // TODO
 			if ($cupmatch['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
			{
				if ($cs_option['scores'] == 1)
					$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$cupmatch['cupmatches_id']), $cupmatch['cupmatches_score1']);
				if ($cupmatch['cupmatches_winner'] == $cupmatch['team1_id'])
				{
					$matrix[$column][$currow]['class'] .= '-win';
					if ($cs_option['scores'] == 1)
						$matrix[$column+1][$currow]['class'] .= '-win';
				}
				else
				{
					$matrix[$column][$currow]['class'] .= '-loss';
					if ($cs_option['scores'] == 1)
						$matrix[$column+1][$currow]['class'] .= '-loss';
				}
			}		
			else
				$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$cupmatch['cupmatches_id']), 'x');
  	}
		
  	$currow += 2;
		$matrix[$column][$currow] = array('class' => 'cup-grid-team');
  
		$string = '';
		if (!empty($cupmatch['team2_name']))
   		$string = $cupmatch['team2_name'];
//		else
//   		$string = '#'.$cupmatch['cupmatches_seed2'];
			
		if ($cs_option['scores'] == 1)
			$matrix[$column+1][$currow] = array('class' => 'cup-grid-score');
 	  if (!empty($string))
 	  {
			$matrix[$column][$currow]['content'] = $string; // TODO
 			if ($cupmatch['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
			{
				if ($cs_option['scores'] == 1)
					$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$cupmatch['cupmatches_id']), $cupmatch['cupmatches_score2']);
				if ($cupmatch['cupmatches_winner'] == $cupmatch['team2_id'] && $cupmatch['team2_id'] != $cupmatch['team1_id'])
				{
					$matrix[$column][$currow]['class'] .= '-win';
					if ($cs_option['scores'] == 1)
						$matrix[$column+1][$currow]['class'] .= '-win';
				}
				else
				{
					$matrix[$column][$currow]['class'] .= '-loss';
					if ($cs_option['scores'] == 1)
						$matrix[$column+1][$currow]['class'] .= '-loss';
				}
			}		
			else
				$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$cupmatch['cupmatches_id']), 'x');
 	  }
 	  
		if ($currow > $maxrows)
			$maxrows = $currow;
  }
  $run++;
}

/* draw LB winner position */
$lbfinal = $cupmatches[$run-1][1];

$column = (($c+1) * ($rounds_1+0.5)*2) - ($c+1 - 1);
$currow = floor(($lbfinal['cupmatches_tree_order']*4) / $max_matches) + 1;
$matrix[$column-2][$currow-1] = array('class' => 'cup-grid-angle-down');
$matrix[$column-2][$currow] = array('class' => 'cup-grid-vertical-split-right');
$matrix[$column-2][$currow+1] = array('class' => 'cup-grid-angle-up');
$matrix[$column-1][$currow] = array('class' => 'cup-grid-horizontal');
$matrix[$column][$currow] = array('class' => 'cup-grid-winner-lb');

if ($lbfinal['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
{
	$string = '';
 	if ($lbfinal['cupmatches_winner'] == $lbfinal['team1_id'])
	{
		if (!empty($lbfinal['team1_name']))
			$string = $lbfinal['team1_name'];
//			else
//				$string = '#'.$lbfinal['cupmatches_seed1'];
	}
	else
	{
		if (!empty($lbfinal['team2_name']))
			$string = $lbfinal['team2_name'];
//			else
//				$string = '#'.$lbfinal['cupmatches_seed2'];
	}
	if (!empty($string))
		$matrix[$column][$currow]['content'] = $string; // TODO
}

$colwidth = 0;
$linewidth = CS_CUPS_GRID_IMAGE_WIDTH;
$xwidth = CS_CUPS_GRID_IMAGE_WIDTH;
if ($cs_option['scores'] == 1)
	$xwidth += 20;
if ($width > 0)
{
	$colwidth = floor(ceil($width - ($rounds + 1)*2*$xwidth) / ($rounds + 1));
}

$start = !empty($_GET['start']) ? (float) ((int) $_GET['start'] / 2) : 0;
$max = !empty($_GET['max']) ? (float) ((int) $_GET['max'] / 2) : 0;  

$startcolumn = 1;
if ($start >= 1 && $start <= $rounds)
	$startcolumn = $start * 2 * ($c+1);
$stoprounds = $rounds + 1;
if ($max >= 1 && $start + $max <= $rounds + 1)
	$stoprounds = $start + $max;

$grid = '';
$grid .= '<table class="cup-grid">'."\n";

for ($j = 0; $j <= $maxrows; $j++)
{
	$grid .= '<tr>'."\n";
	for ($i = $startcolumn; $i <= ($stoprounds * 2 * ($c+1)) - 1; $i++)
	{
		if (isset($matrix[$i][$j]))
		{
		  switch($matrix[$i][$j]['class'])
			{
			default:
        $grid .= '<td class="'.$matrix[$i][$j]['class'].'">?'.$matrix[$i][$j]['class'].'</td>';
        break;
			case 'cup-grid-angle-down-right':
			case 'cup-grid-angle-down':
			case 'cup-grid-angle-up-right':
			case 'cup-grid-angle-up':
			case 'cup-grid-vertical-split-right':
			case 'cup-grid-vertical':
			case 'cup-grid-horizontal':
				$grid .= '<td class="'.$matrix[$i][$j]['class'].'">&nbsp;</td>';
        break;
			case 'cup-grid-winner-lb':
			case 'cup-grid-team-lb':
			case 'cup-grid-team':
			case 'cup-grid-team-lb-win':
			case 'cup-grid-team-win':
			case 'cup-grid-team-lb-loss':
			case 'cup-grid-team-loss':
         $grid .= '<td class="'.$matrix[$i][$j]['class'].'" style="width: '.$colwidth.'px;">';
        if (isset($matrix[$i][$j]['content']))
          $grid .= $matrix[$i][$j]['content'];
        else
          $grid .= '&nbsp;';
        $grid .= '</td>';
        break;
			case 'cup-grid-score-lb':
			case 'cup-grid-score':
			case 'cup-grid-score-lb-win':
			case 'cup-grid-score-win':
			case 'cup-grid-score-lb-loss':
			case 'cup-grid-score-loss':
        $grid .= '<td align="right" class="'.$matrix[$i][$j]['class'].'">';
        if (isset($matrix[$i][$j]['content']))
          $grid .= $matrix[$i][$j]['content'];
        $grid .= '</td>';
        break;
			}
		}
		else
      $grid .= '<td></td>';
	}
	$grid .= "\n".'</tr>'."\n";
}
$grid .= '</table>'."\n";

$datahtml['tree']['grid'] = $grid;
$datahtml['if']['standalone'] = false;
if (!$tree)
	$datahtml['if']['standalone'] = true;

if ($gridonly)
	$cache = $grid;
else
	$cache = cs_subtemplate(__FILE__, $datahtml, 'cups', 'viewtree');

if (function_exists('cs_datacache_load'))
	cs_datacache_create('cups', 'viewtree', $key, $cache, 0);
  
echo $cache;
