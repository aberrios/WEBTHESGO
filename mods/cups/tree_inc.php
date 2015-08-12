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

$halfteams = $count_matches / 2;

// Style-Defs
$yspace_enemies = 4;
$yspace_normal = 8;
$xspace = 15;
$space_top = $currheight = 45;
$space_bottom = 5;
$space_left = $currwidth = 15;
$space_right = 10;
$entityheight = 25;

//$height = $count_matches * ($entityheight + $yspace_normal/2 + $yspace_enemies/2) + $space_top + $space_bottom;
//$width = empty($_GET['width']) ? 600 : $_GET['width'];
$height = empty($cs_option['height']) ? $count_matches * ($entityheight + $yspace_normal/2 + $yspace_enemies/2) + $space_top + $space_bottom : $cs_option['height'];
$width = empty($_GET['width']) ? (empty($cs_option['width']) ? 600 : $cs_option['width']) : (int) $_GET['width'];


$img = imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');

$ocol = explode(',', $cs_option['color_bg']);
$col_bg = imagecolorallocate($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_line']);
$col_line = imagecolorallocate($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_title1']);
$col_csp_red = imagecolorallocate ($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_title2']);
$col_csp_grey = imagecolorallocate ($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_headline']);
$col_cup_headline = imagecolorallocate ($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_team_bg']);
$col_team_bg = imagecolorallocate ($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_team_bg_lb']);
$col_team_bg_lb = imagecolorallocate ($img, $ocol[0], $ocol[1], $ocol[2]);
$ocol = explode(',', $cs_option['color_team_fg']);
$col_team_font = imagecolorallocate ($img, $ocol[0], $ocol[1], $ocol[2]);

$font_csp = 3;
$font_csp_width = imagefontwidth($font_csp);
$font_cup_headline = 2;
$font_match = 3;
$font_match_height = imagefontheight($font_match);

// Set background
imagefilledrectangle($img, 0,0, $width, $height, $col_bg);

// Headline
$title1 = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $cs_option['title1']);
$title2 = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', $cs_option['title2']);
$cupname = iconv($cs_main['charset'], CS_CUPS_GD_CHARSET.'//TRANSLIT', ' - '.$cs_lang['cup'].': ' . $cup['cups_name'].$lb);
imagestring($img, $font_csp, 15, 15, $title1, $col_csp_red);
imagestring($img, $font_csp, $font_csp_width * iconv_strlen($title1, CS_CUPS_GD_CHARSET) + 15, 15, $title2, $col_csp_grey);
imagestring($img, $font_cup_headline, $font_csp_width * (iconv_strlen($title1, CS_CUPS_GD_CHARSET) + iconv_strlen($title2, CS_CUPS_GD_CHARSET)) + 15, 15, $cupname, $col_cup_headline);

// $entityheight = round(($height - $space_top - $space_bottom - $halfteams * $yspace_enemies - $halfteams * $yspace_normal) / $cup['cups_teams']) ;
$entitywidth = round(($width - $space_left - $space_right - ($rounds + 1) * $xspace) / ($rounds + 1));
$entity_font_height = round($entityheight / 2 - $font_match_height / 2);
$entityheight_2 = round($entityheight / 2);
$yspace_normal_2 = round($yspace_normal / 2);

// "Cached" variables
$nexthalf = $halfteams;
$max = $nexthalf;
