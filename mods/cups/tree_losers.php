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

# Overwrite global settings by using the following array
$cs_main = array('init_sql' => true, 'init_tpl' => false, 'init_mod' => true);

chdir('../../');

require_once 'system/core/functions.php';

cs_init($cs_main);
@error_reporting(E_ALL);

$cs_lang = cs_translate('cups');
$cs_option = cs_sql_option(__FILE__, 'cups');

chdir('mods/cups/');
include_once 'defines.php';
include_once 'functions.php';

$cups_id = (int) $_GET['id'];

$cup = cs_sql_select(__FILE__, 'cups', 'cups_teams, cups_name, cups_system, cups_access, cups_brackets', 'cups_id = ' . $cups_id);
$rounds = cs_cups_log($cup['cups_teams']);
$rounds_1 = (float) $rounds - 0.5;

if ($account['access_cups'] < $cup['cups_access'] || $cup['cups_access'] == 0 || $cup['cups_brackets'] != CS_CUPS_SYSTEM_LB)
{
	$height = 100;
	$width = empty($cs_option['width']) ? 600 : $cs_option['width'];

	$img = imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');

	// Set background
	$col_bg = imagecolorallocate($img, 255, 255, 255);
	imagefilledrectangle($img, 0,0, $width, $height, $col_bg);

	$font_csp = 3;
	$col_csp_red = imagecolorallocate($img, 186, 22, 22);
	$fw = imagefontwidth($font_csp);
	$fh = imagefontheight($font_csp);
	$top = floor($height-$fh) / 2;
	if ($top < 0) $top = 0;
	$left = ($width-$fw*iconv_strlen($cs_lang['access_denied'], $cs_main['charset'])) / 2;
	if ($left < 0) $left = 0;
	imagestring($img, $font_csp, $left, $top, $cs_lang['access_denied'], $col_csp_red);

	header ('Content-type: image/png');
	imagepng($img);
	imagedestroy($img);
  exit(0);
}

$width = empty($cs_option['width']) ? (empty($_GET['width']) ? 600 : (int) $_GET['width']) : $cs_option['width'];
$key = 'lang='.$account['users_lang'].'&cup='.$cups_id.'&lb=1&width='.$width.'&access='.$account['access_cups'];
if (function_exists('cs_datacache_load'))
	$cachedata = cs_datacache_load('cups', 'image', $key, false);
else
	$cachedata = false;
if ($cachedata !== false)
{
	header ('Content-type: image/png');
	echo base64_decode($cachedata);
	exit(0);
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
$cells .= ', cm.cupmatches_nextmatch AS cupmatches_nextmatch, cm.cupmatches_match AS cupmatches_match';

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
  	$match['team1_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team1_id'], $match['team1_name'], $match['autoseed1'], $match['seed1'], false, true);
  	$match['team2_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team2_id'], $match['team2_name'], $match['autoseed2'], $match['seed2'], false, true);
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
	$height = 100;
	$width = empty($cs_option['width']) ? 600 : $cs_option['width'];

	$img = imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');

	// Set background
	$col_bg = imagecolorallocate($img, 255, 255, 255);
	imagefilledrectangle($img, 0,0, $width, $height, $col_bg);

	$font_csp = 3;
	$col_csp_red = imagecolorallocate($img, 186, 22, 22);
	$fw = imagefontwidth($font_csp);
	$fh = imagefontheight($font_csp);
	$top = floor($height-$fh) / 2;
	if ($top < 0) $top = 0;
	$left = ($width-$fw*iconv_strlen($cs_lang['no_matches'], $cs_main['charset'])) / 2;
	if ($left < 0) $left = 0;
	imagestring($img, $font_csp, $left, $top, $cs_lang['no_matches'], $col_csp_red);

	header ('Content-type: image/png');
	ob_start();
	imagepng($img);
	$imagevariable = ob_get_contents();
	ob_end_clean();

	if (function_exists('cs_datacache_load'))
		cs_datacache_create('cups', 'image', $key, base64_encode($imagevariable), 0);

	echo $imagevariable;
	imagedestroy($img);
  exit(0);
}

$max_grid = ($max_tree_order*4) / $max_matches;
$max_pos = ($max_grid % 2 == 0) ? $max_grid + 2 : $max_grid + 1;

// Calc-Defs
$count_matches = $max_pos;
$maxrounds = $rounds;
$rounds = (int) ($rounds_1 * 2) - 1;
$lb = ' (LB)';
include 'tree_inc.php';
$rounds = $maxrounds;


$count_cupmatches = 0;
$result = $cup['cups_teams'];
while ($result > 2) { $result /= 2; $count_cupmatches += $result; }
$count_cupmatches += 2;
$round = 0;
$run = 0;
$match_run = 0;
$baseheight = $space_top;

$run = 0;
$prevmatches = array();
for ($i=1.0; $i <= $rounds_1; $i += 0.5) {
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
		$gridpos = (float) ($cupmatch['cupmatches_tree_order']*4) / $max_matches;
  	$currheight = $baseheight + (int) ($gridpos * $entityheight);
  
		if ($i > 1.0)
		{
			if (count($prevmatches[$cupmatch['cupmatches_match']]) == 1)
			{
				$pmatch1 = $prevmatches[$cupmatch['cupmatches_match']][0];
				$prevgridpos1 = (float) ($pmatch1[1]*4.0) / $max_matches;
  			$prevwidth = $currwidth - floor($xspace / 2);
				$prevheight = $baseheight + (int) ($prevgridpos1 * $entityheight) + $entityheight + floor($yspace_enemies / 2);
				// $lineheight = $currheight + $entityheight + floor($yspace_enemies / 2);
				$lineheight = $currheight + floor($entityheight * 1.5) + $yspace_enemies;
				imageline($img, $prevwidth, $lineheight, $prevwidth, $prevheight, $col_line);
				imageline($img, $prevwidth-floor($xspace / 2), $prevheight, $prevwidth, $prevheight, $col_line);
				imageline($img, $currwidth-floor($xspace / 2), $lineheight, $currwidth, $lineheight, $col_line);
			}
			else if (count($prevmatches[$cupmatch['cupmatches_match']]) == 2)
			{
				$pmatch1 = $prevmatches[$cupmatch['cupmatches_match']][0];
				$pmatch2 = $prevmatches[$cupmatch['cupmatches_match']][1];
				$prevgridpos1 = (float) ($pmatch1[1]*4.0) / $max_matches;
  			$prevwidth = $currwidth - floor($xspace / 2);
				$prevheight = $baseheight + (int) ($prevgridpos1 * $entityheight) + $entityheight + floor($yspace_enemies / 2);
				$prevgridpos2 = (float) ($pmatch2[1]*4.0) / $max_matches;
  			$prevwidth2 = $currwidth - floor($xspace / 2);
  			$prevheight2 = $baseheight + (int) ($prevgridpos2 * $entityheight) + $entityheight + floor($yspace_enemies / 2);
				imageline($img, $prevwidth, $prevheight, $prevwidth2, $prevheight2, $col_line);
				imageline($img, $prevwidth-floor($xspace / 2), $prevheight, $prevwidth, $prevheight, $col_line);
				imageline($img, $prevwidth-floor($xspace / 2), $prevheight2, $prevwidth, $prevheight2, $col_line);
				$lineheight = $currheight + $entityheight + floor($yspace_enemies / 2);
				imageline($img, $currwidth-floor($xspace / 2), $lineheight, $currwidth, $lineheight, $col_line);
			}
		}
		if ($i - floor($i) == 0.0)
		{
  		imagefilledrectangle ($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg);
		}
		else
  		imagefilledrectangle ($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg_lb);

		$string = '';
		if (!empty($cupmatch['team1_name']))
   		$string = $cupmatch['team1_name'];
//		else
//   		$string = '#'.$cupmatch['cupmatches_seed1'];
  	$string = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $string);
  	if (!empty($string)) imagestring($img, $font_match, $currwidth + 10, $currheight + $entity_font_height, $string, $col_team_font);
 	 
		
		if ($i == 1.0)
  		$currheight = $baseheight + (int) ($gridpos * $entityheight) + $entityheight + $yspace_enemies;
		else
  		$currheight = $baseheight + (int) ($gridpos * $entityheight) + $entityheight + $yspace_enemies;

  	imagefilledrectangle ($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg);
  
		$string = '';
		if (!empty($cupmatch['team2_name']))
   		$string = $cupmatch['team2_name'];
//		else
//   		$string = '#'.$cupmatch['cupmatches_seed2'];
			
 	  $string = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $string);
 	  if (!empty($string)) imagestring($img, $font_match, $currwidth + 10, $currheight + $entity_font_height, $string, $col_team_font);
  
  }
  $run++;
  $currwidth += $entitywidth + $xspace;
  $nexthalf /= 2;
  $max += $nexthalf;
}
/* draw LB winner position */
$lbfinal = $cupmatches[$run-1][1];

$currheight -= floor($entityheight / 2) + floor($yspace_enemies / 2);
$prevwidth = $currwidth - floor($xspace / 2);
$lineheight = $currheight + $entityheight + floor($yspace_enemies / 2);
imageline($img, $prevwidth-floor($xspace / 2), $currheight + floor($entityheight/2), $currwidth, $currheight + floor($entityheight/2), $col_line);

imagefilledrectangle ($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg);

if ($lbfinal['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
{
	$string = '';
	if ($lbfinal['cupmatches_winner'] == $final['team1_id'])
	{
		if (!empty($lbfinal['team1_name']))
			$string = $lbfinal['team1_name'];
//			else
//				$string = '#'.$match['cupmatches_seed1'];
	}
	else
	{
		if (!empty($lbfinal['team2_name']))
			$string = $lbfinal['team2_name'];
//			else
//				$string = '#'.$match['cupmatches_seed2'];
	}
	$string = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $string);
	if (!empty($string)) imagestring($img, $font_match, $currwidth + 10, $currheight + $entity_font_height, $string, $col_team_font);
}

header ('Content-type: image/png');
ob_start();
imagepng($img);
$imagevariable = ob_get_contents();
ob_end_clean();

if (function_exists('cs_datacache_load'))
	cs_datacache_create('cups', 'image', $key, base64_encode($imagevariable), 0);
echo $imagevariable;
imagedestroy($img);
  
