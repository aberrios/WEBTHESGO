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

# Overwrite global settings by using the following array
$cs_main = array('init_sql' => true, 'init_tpl' => false, 'init_mod' => true);

chdir('../../');

require_once 'system/core/functions.php';

cs_init($cs_main);
@error_reporting(E_ALL);

$cs_lang = cs_translate('cups');
$cs_option = cs_sql_option(__FILE__, 'cups');

include_once 'mods/cups/defines.php';
include_once 'mods/cups/functions.php';

$key = 'lang='.$account['users_lang'].'&access='.$account['access_cups'];
if (function_exists('cs_datacache_load'))
	$cachedata = cs_datacache_load('cups', 'style', $key, false);
else
	$cachedata = false;
if ($cachedata !== false)
{
	header ('Content-type: text/css');
	echo $cachedata;
	exit(0);
}

$data = array();

$width = empty($cs_option['width']) ? (empty($_GET['width']) ? 0 : (int) $_GET['width']) : $cs_option['width'];
$height = empty($cs_option['height']) ? 0 : $cs_option['height'];

$data['grid']['width'] = ($width > 0 ? 'width: '.$width.'px;' : '');
$data['grid']['height'] = ($height > 0 ? 'height: '.$height.'px;' : '');
$data['grid']['score_width'] = CS_CUPS_GRID_SCORE_WIDTH;
$data['grid']['image_width'] = CS_CUPS_GRID_IMAGE_WIDTH;
$data['grid']['image_height'] = CS_CUPS_GRID_IMAGE_HEIGHT;

$ocol = cs_cups_dechex(explode(',', $cs_option['color_bg']));
$col_bg = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_team_fg']));
$col_team_font = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_team_bg']));
$col_team_bg = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_team_bg_lb']));
$col_team_bg_lb = $ocol[0].$ocol[1].$ocol[2];

$data['grid']['color_bg'] = $col_bg;
$data['grid']['color_team_font'] = $col_team_font;
$data['grid']['color_team_bg'] = $col_team_bg;
$data['grid']['color_team_bg_lb'] = $col_team_bg_lb;

header ('Content-type: text/css');

$style = cs_subtemplate(__FILE__, $data, 'cups', 'tree_style');
if (function_exists('cs_datacache_load'))
	cs_datacache_create('cups', 'style', $key, $style, 0);
echo $style;
exit(0);
