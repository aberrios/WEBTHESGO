<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

/* Cup tree html generation code:
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
$entityheight = 15;

$height = empty($cs_option['height']) ? 0 : $cs_option['height'];
$width = empty($_GET['width']) ? (empty($cs_option['width']) ? 600 : $cs_option['width']) : (int) $_GET['width'];

$ocol = cs_cups_dechex(explode(',', $cs_option['color_bg']));
$col_bg = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_line']));
$col_line = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_title1']));
$col_csp_red = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_title2']));
$col_csp_grey = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_headline']));
$col_cup_headline = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_team_bg']));
$col_team_bg = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_team_bg_lb']));
$col_team_bg_lb = $ocol[0].$ocol[1].$ocol[2];
$ocol = cs_cups_dechex(explode(',', $cs_option['color_team_fg']));
$col_team_font = $ocol[0].$ocol[1].$ocol[2];

$datahtml['tree']['style1'] = 'color: #'.$col_csp_red.';';
$datahtml['tree']['title1'] = $cs_option['title1'];
$datahtml['tree']['style2'] = 'color: #'.$col_csp_grey.';';
$datahtml['tree']['title2'] = $cs_option['title2'];
$datahtml['tree']['style3'] = 'color: #'.$col_cup_headline.';';
$datahtml['tree']['title3'] = ' - '.$cs_lang['cup'].': ' . $cup['cups_name'].$lb;

$gwhere = 'games_id = ' . $cup['games_id'];
$cs_game = cs_sql_select(__FILE__,'games','games_name, games_id', $gwhere);
$gameicon = 'uploads/games/'.$cup['games_id'].'.gif';
$datahtml['game']['icon'] = file_exists($gameicon) ? cs_html_img($gameicon, 0, 0, 0, $cs_game['games_name'], $cs_game['games_name']) : '';

/* the number of columns per round. LB needs one extra. */
if ($cs_option['scores'] == 1)
	$c = 3;
else
	$c = 2; 