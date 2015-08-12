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

$cs_lang = cs_translate('cups');

include_once 'mods/cups/functions.php';

$data = array();

$start = !empty($_GET['start']) ? (int) $_GET['start'] : 0;
$games_id = !empty($_REQUEST['games_id']) ? (int) $_REQUEST['games_id'] : 0;

// we are only interested in cups which have started,
// so at least 1 cupmatch must exist
$table = 'cups cup LEFT JOIN {pre}_cupmatches cm ON cm.cups_id = cup.cups_id LEFT JOIN {pre}_games ga ON ga.games_id = cup.games_id';
$where = 'cm.cupmatches_id IS NOT NULL AND cup.cups_access <= '.$account['access_cups'].' AND cup.cups_access <> 0';
if ($games_id > 0)
	$where .= ' AND cup.games_id = '.$games_id;
else
	$games_id = 0;
$select = 'DISTINCT(cup.cups_id), cup.*, ga.games_name';
$order = 'cup.cups_start DESC';

$data_games = cs_sql_select(__FILE__,'games','games_name, games_id',0,'games_name',0,0);
$data['games'] = cs_dropdownsel($data_games,$games_id,'games_id');

$count = cs_sql_count(__FILE__, $table, $where, 'cup.cups_id');

if ($start < 0 || $start >= $count)
	$start = 0;

$key = 'lang='.$account['users_lang'].'&games_id='.$games_id.'&start='.$start.'&access='.$account['access_cups'];
if (function_exists('cs_datacache_load'))
        $cachedata = cs_datacache_load('cups', 'halloffame', $key, false);
else
        $cachedata = false;
if ($cachedata !== false)
{
	echo $cachedata;
	return;
}

if ($count)
{
	$data['if']['hascups'] = true;
	$cups = cs_sql_select(__FILE__, $table, $select, $where, $order, $start, 0); // TODO: pagination $account['users_limit']
	$data['cups'] = array();
	$data['count']['all'] = $count;
	$run = 0;
	/* take icons from awards module */
	$data['image']['gold'] = cs_html_img('symbols/awards/pokal_gold.png');
	$data['image']['silver'] = cs_html_img('symbols/awards/pokal_silber.png');
	$data['image']['bronze'] = cs_html_img('symbols/awards/pokal_bronze.png');
	foreach ($cups as $cup)
	{
		$data['cups'][$run] = array();
		$data['cups'][$run]['cups_id'] = $cup['cups_id'];
		$data['cups'][$run]['cups_name'] = $cup['cups_name'];
		$data['cups'][$run]['game_name'] = $cup['games_name'];
		$gameicon = 'uploads/games/'.$cup['games_id'].'.gif';
		$data['cups'][$run]['game_icon'] = file_exists($gameicon) ? cs_html_img($gameicon, 0, 0, 0, $cup['games_name'], $cup['games_name']) : '';
		$data['cups'][$run]['cups_start'] = cs_date('unix',$cup['cups_start'],1);
		$data['cups'][$run]['cups_system'] = ($cup['cups_system'] == CS_CUPS_TYPE_USERS) ? $cs_lang['user_vs_user'] : $cs_lang['team_vs_team'];

		/* check for still open matches */
		$open = cs_sql_count(__FILE__, 'cupmatches', 'cups_id = '.$cup['cups_id'].' AND cupmatches_winner = '.CS_CUPS_TEAM_UNKNOWN);
		if ($open == 0)
		{
			$data['cups'][$run]['if']['open'] = false;
			if ($cup['cups_system'] == CS_CUPS_TYPE_USERS)
			{
				$tablem = 'cupmatches cm ';
			  $tablem .= 'LEFT JOIN {pre}_users u1 ON cm.squad1_id = u1.users_id ';
				$tablem .= 'LEFT JOIN {pre}_users u2 ON cm.squad2_id = u2.users_id';
				$selectm = 'cm.*, u1.users_nick AS team1_name, u2.users_nick AS team2_name';
			}
			else
			{
				$tablem = 'cupmatches cm ';
			  $tablem .= 'LEFT JOIN {pre}_squads sq1 ON cm.squad1_id = sq1.squads_id ';
				$tablem .= 'LEFT JOIN {pre}_squads sq2 ON cm.squad2_id = sq2.squads_id';
				$selectm = 'cm.*, sq1.squads_name AS team1_name, sq2.squads_name AS team2_name';
			}
			/* which match to check depends on the bracket system */
			switch ($cup['cups_brackets'])
			{
			case CS_CUPS_SYSTEM_KO:
				/* final in WB determines winner and 2nd,
				 * losers from previous matches determines 3-4
         */
				/* the final is the match with no next match in the WB */
				$matchw = cs_sql_select(__FILE__, $tablem, $selectm, 'cups_id = '.$cup['cups_id'].' AND cupmatches_nextmatch = '.CS_CUPS_NO_NEXTMATCH, 0, 0, 1);
				/* the 2 losers matches point to the final */
				$matchl = cs_sql_select(__FILE__, $tablem, $selectm, 'cups_id = '.$cup['cups_id'].' AND cupmatches_nextmatch = '.$matchw['cupmatches_match'], 0, 0, 2);
				$data['cups'][$run]['third'] = array();
				$count = 0;
				/* third/fourth are the teams that lost the semi finals */
				if ($matchl[0]['squad1_id'] != $matchl[0]['cupmatches_winner'])
				{
					if ($matchl[0]['squad1_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][$count++] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl[0]['squad1_id'], $matchl[0]['team1_name'], 1, 9999, true));
				}
				else
				{
					if ($matchl[0]['squad2_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][$count++] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl[0]['squad2_id'], $matchl[0]['team2_name'], 1, 9999, true));
				}
				if ($matchl[1]['squad1_id'] != $matchl[1]['cupmatches_winner'])
				{
					if ($matchl[1]['squad1_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][$count++] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl[1]['squad1_id'], $matchl[1]['team1_name'], 1, 9999, true));
				}
				else
				{
					if ($matchl[1]['squad2_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][$count++] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl[1]['squad2_id'], $matchl[1]['team2_name'], 1, 9999, true));
				}
				break;
			case CS_CUPS_SYSTEM_KO3RD:
				/* final in WB determines winner and 2nd,
	       * losers from third place game determines 3rd
         */
				/* the final is the match with no next match in the WB and which is not match #0 */
				$matchw = cs_sql_select(__FILE__, $tablem, $selectm, 'cups_id = '.$cup['cups_id'].' AND cupmatches_match <> 0 AND cupmatches_nextmatch = '.CS_CUPS_NO_NEXTMATCH, 0, 0, 1);
				/* the 3rd place match is always match #0  */
				$matchl = cs_sql_select(__FILE__, $tablem, $selectm, 'cups_id = '.$cup['cups_id'].' AND cupmatches_match = 0', 0, 0, 1);
				$data['cups'][$run]['third'] = array();
				/* third is the team that won the 3rd place match */
				if ($matchl['squad1_id'] == $matchl['cupmatches_winner'])
				{
					if ($matchl['squad1_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][0] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl['squad1_id'], $matchl['team1_name'], 1, 9999, true));
				}
				else
				{
					if ($matchl['squad2_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][0] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl['squad2_id'], $matchl['team2_name'], 1, 9999, true));
				}
				break;
			case CS_CUPS_SYSTEM_LB:
				/* grand final determines winner and 2nd,
				 * losers bracket final determines 3rd
         */
				/* the grand final is always match #0 */
				$matchw = cs_sql_select(__FILE__, $tablem, $selectm, 'cups_id = '.$cup['cups_id'].' AND cupmatches_match = 0', 0, 0, 1);
				/* the loser bracket final is the match that is in the loserbracket and has match #0 as nextmatch */
				$matchl = cs_sql_select(__FILE__, $tablem, $selectm, 'cups_id = '.$cup['cups_id'].' AND cupmatches_nextmatch = 0 AND cupmatches_loserbracket = 1', 0, 0, 1);
				$data['cups'][$run]['third'] = array();
				/* third is the team that lost the LB final */
				if ($matchl['squad1_id'] != $matchl['cupmatches_winner'])
				{
					if ($matchl['squad1_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][0] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl['squad1_id'], $matchl['team1_name'], 1, 9999, true));
				}
				else
				{
					if ($matchl['squad2_id'] != CS_CUPS_TEAM_BYE)
						$data['cups'][$run]['third'][0] = array('name' => cs_cups_team($cs_lang, $cup['cups_system'], $matchl['squad2_id'], $matchl['team2_name'], 1, 9999, true));
				}
				break;
			}
			if ($matchw['cupmatches_winner'] == $matchw['squad1_id'])
			{
				$data['cups'][$run]['winner'] = cs_cups_team($cs_lang, $cup['cups_system'], $matchw['squad1_id'], $matchw['team1_name'], 1, 9999, true);
				$data['cups'][$run]['second'] = cs_cups_team($cs_lang, $cup['cups_system'], $matchw['squad2_id'], $matchw['team2_name'], 1, 9999, true);
			}
			else
			{
				$data['cups'][$run]['winner'] = cs_cups_team($cs_lang, $cup['cups_system'], $matchw['squad2_id'], $matchw['team2_name'], 1, 9999, true);
				$data['cups'][$run]['second'] = cs_cups_team($cs_lang, $cup['cups_system'], $matchw['squad1_id'], $matchw['team1_name'], 1, 9999, true);
			}
		}
		else
			$data['cups'][$run]['if']['open'] = true;
		$run++;
	}
}
else
{
	$data['if']['hascups'] = false;
	$data['count']['all'] = 0;
}

$cachedata = cs_subtemplate(__FILE__, $data, 'cups', 'halloffame');
if (function_exists('cs_datacache_load'))
        cs_datacache_create('cups', 'halloffame', $key, $cachedata, 0);
echo $cachedata;

?>
