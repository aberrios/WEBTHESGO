<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: tree.php 4233 2010-07-05 01:55:14Z Spongebob $

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

if (!empty($cs_main)) {
  
	$cs_lang = cs_translate('cups');
	include_once 'mods/cups/defines.php';
	$cs_option = cs_sql_option(__FILE__, 'cups');
  $data = array();
  $data['cups']['id'] = (int) $_GET['id'];
   $cup = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = ' . $data['cups']['id']);
	if (empty($cup['cups_id'])) {
		cs_redirect($cs_lang['no_selection'], 'cups', 'list');
	}
  $cond_de = (bool) cs_sql_count(__FILE__, 'cups', 'cups_id = ' . $data['cups']['id'] . ' AND cups_brackets = '.CS_CUPS_SYSTEM_LB);
  $cond_ko3rd = (bool) cs_sql_count(__FILE__, 'cups', 'cups_id = ' . $data['cups']['id'] . ' AND cups_brackets = '.CS_CUPS_SYSTEM_KO3RD);
//  $cond_2 = cs_sql_count(__FILE__,'cupsquads','cups_id = ' . $data['cups']['id']) - $cup['cups_teams'] / 2;
//  if ($cond_1 AND $cond_2 > 1)
  if ($cond_de)
    $data['if']['brackets'] = TRUE;
  else
    $data['if']['brackets'] = FALSE;
  if ($cond_de || $cond_ko3rd)
    $data['if']['extra'] = TRUE;
  else
    $data['if']['extra'] = FALSE;
  $data['cup'] = $cup; 
  $data['options'] = $cs_option;
  if ($cs_option['html'] == 1)
  {
  	define('IN_TREE', true);
		$gridonly = true;
		ob_start();
		include_once('mods/cups/viewtree.php');
		$data['grid']['wb'] = ob_get_contents();
		ob_end_clean();
		if ($cond_de)
		{  	
			ob_start();
			include_once('mods/cups/viewtree_losers.php');
			$data['grid']['lb'] = ob_get_contents();
			ob_end_clean();
		}
		if ($cond_de || $cond_ko3rd)
		{
			ob_start();
			include_once('mods/cups/viewtree_extra.php');
			$data['grid']['extra'] = ob_get_contents();
			ob_end_clean();
		}
    $data['if']['html'] = TRUE;
  }
  else
    $data['if']['html'] = FALSE;
	$gameicon = 'uploads/games/'.$cup['games_id'].'.gif';
	$where = 'games_id = ' . $cup['games_id'];
	$cs_game = cs_sql_select(__FILE__,'games','games_name, games_id', $where);
	$data['game']['icon'] = file_exists($gameicon) ? cs_html_img($gameicon, 0, 0, 0, $cs_game['games_name'], $cs_game['games_name']) : '';
  
  echo cs_subtemplate(__FILE__, $data, 'cups', 'tree');
}
else {
 
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

$cup = cs_sql_select(__FILE__, 'cups', 'cups_id, cups_teams, cups_name, cups_system, cups_access, cups_brackets', 'cups_id = ' . $cups_id);
if (empty($cup['cups_id'])) {
	cs_redirect($cs_lang['no_selection'], 'cups', 'list');
}
$rounds = cs_cups_log($cup['cups_teams']);
$rounds_1 = $rounds;

if ($account['access_cups'] < $cup['cups_access'] || $cup['cups_access'] == 0)
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
$key = 'lang='.$account['users_lang'].'&cup='.$cups_id.'&lb=0&width='.$width.'&access='.$account['access_cups'];
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
$cells .= ', cm.cupmatches_match AS cupmatches_match, cm.cupmatches_nextmatch AS cupmatches_nextmatch';

$where = 'cm.cups_id = ' . $cups_id . ' AND cm.cupmatches_loserbracket = 0 AND cm.cupmatches_round = ';

$cupmatches = array();
for ($i=1; $i <= $rounds; $i++) {
  $temp = cs_sql_select(__FILE__, $tables, $cells, $where . $i, 'cm.cupmatches_tree_order ASC',0,0);
	$run = 0;
	if (count($temp))
  foreach ($temp as $match)
  {
  	$match['team1_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team1_id'], $match['team1_name'], $match['autoseed1'], $match['seed1'], false, true);
  	$match['team2_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team2_id'], $match['team2_name'], $match['autoseed2'], $match['seed2'], false, true);
    $cupmatches[$i][$run++] = $match;
  }
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

// Calc-Defs
$count_matches = $cup['cups_teams'];
$lb = '';
include 'tree_inc.php';


$round = 0;
$run = 0;
$match_run = 0;

$prevmatches = array();
$baseheight = $space_top;
for ($i = 1; $i <= $rounds; $i++)
{
	$run = 0;
	foreach($cupmatches[$i] as $match)
	{
		if ($i == 1)
		{
			/* calculate height */
			$currheight = $baseheight + $run*(2*$entityheight + $yspace_enemies + $yspace_normal);
		}
		else
		{
			/* calculate height */
			$currheight = floor(($prevmatches[$match['cupmatches_match']][0][1] + $prevmatches[$match['cupmatches_match']][0][2]) / 2) - floor($entityheight / 2);

			/* draw prev lines */
			$prevwidth1 = $currwidth - $xspace;
			$prevwidth2 = $currwidth - floor($xspace / 2);
			$prevheight1 = $prevmatches[$match['cupmatches_match']][0][1] + floor($entityheight / 2);
			$prevheight2 = $prevmatches[$match['cupmatches_match']][0][2] - floor($entityheight / 2);
			$betweenheight = $currheight + floor($entityheight / 2);
			/* draw from first prev match */
			imageline($img, $prevwidth1, $prevheight1, $prevwidth2, $prevheight1, $col_line);
			/* draw from second prev match */
			imageline($img, $prevwidth1, $prevheight2, $prevwidth2, $prevheight2, $col_line);
			/* draw vertical line between */
			imageline($img, $prevwidth2, $prevheight1, $prevwidth2, $prevheight2, $col_line);
			/* draw to this one */
			imageline($img, $prevwidth2, $betweenheight, $currwidth, $betweenheight, $col_line);
		}
		/* keep track of this height */
		$prevheight = $currheight;
		imagefilledrectangle($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg);

		$string = '';
		if (!empty($match['team1_name']))
			$string = $match['team1_name'];
//		else
//			$string = '#'.$match['cupmatches_seed1'];
		$string = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $string);
		if (!empty($string)) imagestring($img, $font_match, $currwidth + 10, $currheight + $entity_font_height, $string, $col_team_font);

		if ($i == 1)
		{
			/* calculate height */
			$currheight += $entityheight + $yspace_enemies;
		}
		else
		{
			/* draw new position */
			$currheight = floor(($prevmatches[$match['cupmatches_match']][1][1] + $prevmatches[$match['cupmatches_match']][1][2]) / 2) - floor($entityheight / 2);

			/* draw prev lines */
			$prevwidth1 = $currwidth - $xspace;
			$prevwidth2 = $currwidth - floor($xspace / 2);
			$prevheight1 = $prevmatches[$match['cupmatches_match']][1][1] + floor($entityheight / 2);
			$prevheight2 = $prevmatches[$match['cupmatches_match']][1][2] - floor($entityheight / 2);
			$betweenheight = $currheight + floor($entityheight / 2);
			/* draw from first prev match */
			imageline($img, $prevwidth1, $prevheight1, $prevwidth2, $prevheight1, $col_line);
			/* draw from second prev match */
			imageline($img, $prevwidth1, $prevheight2, $prevwidth2, $prevheight2, $col_line);
			/* draw vertical line between */
			imageline($img, $prevwidth2, $prevheight1, $prevwidth2, $prevheight2, $col_line);
			/* draw to this one */
			imageline($img, $prevwidth2, $betweenheight, $currwidth, $betweenheight, $col_line);
		}
    if (!isset($prevmatches[$match['cupmatches_nextmatch']]))
    {
      $prevmatches[$match['cupmatches_nextmatch']] = array();
			$prevmatches[$match['cupmatches_nextmatch']][0] = array($match['cupmatches_match'],
																															$prevheight,
																															$currheight + $entityheight);
    }
    else if ($prevmatches[$match['cupmatches_nextmatch']][0][1] > $currheight)
    {
      $prevmatches[$match['cupmatches_nextmatch']][1] = $prevmatches[$match['cupmatches_nextmatch']][0];
      $prevmatches[$match['cupmatches_nextmatch']][0] = array($match['cupmatches_match'],
																																 $prevheight,
																																 $currheight + $entityheight);
    }
    else
      $prevmatches[$match['cupmatches_nextmatch']][1] = array($match['cupmatches_match'], 
																															 $prevheight,
																															 $currheight + $entityheight);
		imagefilledrectangle($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg);

		$string = '';
		if (!empty($match['team2_name']))
			$string = $match['team2_name'];
//		else
//			$string = '#'.$match['cupmatches_seed1'];
		$string = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $string); 
		if (!empty($string)) imagestring($img, $font_match, $currwidth + 10, $currheight + $entity_font_height, $string, $col_team_font);

		$run++;
	}
  $currwidth += $entitywidth + $xspace;
}
$finalmatchnr = -1;
if ($cup['cups_brackets'] == CS_CUPS_SYSTEM_LB)
	$finalmatchnr = 0;
if (isset($prevmatches[$finalmatchnr]))
{
	/* draw winner position */
	$final = $cupmatches[$rounds][0];

	/* calculate height */
	$currheight = floor(($prevmatches[$finalmatchnr][0][1] + $prevmatches[$finalmatchnr][0][2]) / 2) - floor($entityheight / 2);

	/* draw prev lines */
	$prevwidth1 = $currwidth - $xspace;
	$prevwidth2 = $currwidth - floor($xspace / 2);
	$prevheight1 = $prevmatches[$finalmatchnr][0][1] + floor($entityheight / 2);
	$prevheight2 = $prevmatches[$finalmatchnr][0][2] - floor($entityheight / 2);
	$betweenheight = $currheight + floor($entityheight / 2);
	/* draw from first prev match */
	imageline($img, $prevwidth1, $prevheight1, $prevwidth2, $prevheight1, $col_line);
	/* draw from second prev match */
	imageline($img, $prevwidth1, $prevheight2, $prevwidth2, $prevheight2, $col_line);
	/* draw vertical line between */
	imageline($img, $prevwidth2, $prevheight1, $prevwidth2, $prevheight2, $col_line);
	/* draw to this one */
	imageline($img, $prevwidth2, $betweenheight, $currwidth, $betweenheight, $col_line);

	imagefilledrectangle($img, $currwidth, $currheight, $currwidth + $entitywidth, $currheight + $entityheight, $col_team_bg);

	if ($final['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
	{
		$string = '';
		if ($final['cupmatches_winner'] == $final['team1_id'])
		{
			if (!empty($final['team1_name']))
				$string = $final['team1_name'];
//			else
//				$string = '#'.$match['cupmatches_seed1'];
		}
		else
		{
			if (!empty($final['team2_name']))
				$string = $final['team2_name'];
//			else
//				$string = '#'.$match['cupmatches_seed2'];
		}
		$string = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $string);
		if (!empty($string)) imagestring($img, $font_match, $currwidth + 10, $currheight + $entity_font_height, $string, $col_team_font);
	}
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
exit(0); 
  
}
