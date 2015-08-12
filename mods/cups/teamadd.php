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
$data = array();

include_once 'mods/cups/functions.php';

$cups_id = empty($_POST['id']) ? (empty($_GET['id']) ? 0 : (int) $_GET['id']): (int) $_POST['id'];

$matchcount = cs_sql_count(__FILE__,'cupmatches','cups_id = ' . $cups_id);
if ($matchcount > 0)
{
	echo $cs_lang['cup_started'];
	return;
}

$cs_cup = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = ' . $cups_id);
if (empty($cs_cup['cups_id']))
{
	echo $cs_lang['access_denied'];
	return;
}

if ($cs_cup['cups_system'] == CS_CUPS_TYPE_USERS)
{
	$data['if']['user'] = true;
	$table = 'users sq LEFT JOIN {pre}_cupsquads cs ON sq.users_id = cs.squads_id AND cs.cups_id = '.$cups_id;
	$select = 'sq.users_id AS team_id, sq.users_nick AS team_name, cs.cupsquads_id';
	/* select active, non-deleted users which are not participating */
	$where = 'sq.users_delete = 0 AND sq.users_active = 1 AND cs.cupsquads_id IS NULL';
	$team_name = '';
	/* select a specific user */
	$extra_where = ' AND sq.users_nick = \'%s\'';
} 
else /* teams */
{
	$data['if']['user'] = false;
	$table = 'squads sq LEFT JOIN {pre}_clans cl ON sq.clans_id = cl.clans_id LEFT JOIN {pre}_cupsquads cs ON sq.squads_id = cs.squads_id AND cs.cups_id = '.$cups_id;
	$select = 'sq.squads_id AS team_id, sq.squads_name AS team_name, cl.clans_tag, cs.cupsquads_id';
	/* select squads which are not participating */
	$where = 'cs.cupsquads_id IS NULL';

	$teams = cs_sql_select(__FILE__, $table, $select, $where, 'clans_tag ASC, team_name ASC', 0, 0);
	$run = 0;
	$teamarray = array();
	foreach ($teams as $squad)
	{
		$teamarray[$run]['team_id'] = $squad['team_id'];
		$teamarray[$run]['team_name'] = cs_secure($squad['clans_tag']).' - '.cs_secure($squad['team_name']);
		$run++;
	}
	$team_name = '';
	$team_id = 0;
	if (isset($_POST['submit']))
		$team_id = (int) $_POST['team_id'];
	$data['teams']['select'] = cs_dropdown('team_id','team_name',$teamarray, (!empty($_POST['team_id']) ? (int) $_POST['team_id'] : 0), 'team_id', 0);
	/* select a specific squad */
	$extra_where = ' AND sq.squads_id = %s';
}

$error = '';
$team_id = '';
if (isset($_POST['submit']))
{
	if ($cs_cup['cups_system'] == CS_CUPS_TYPE_USERS)
	{
		$team_id = $_POST['team_name'];
	}
	else
	{
		$team_id = (int) $_POST['team_id'];
	}
	$team = cs_sql_select(__FILE__, $table, $select, $where.sprintf($extra_where, cs_sql_escape($team_id)), 0, 0, 1);
	if (empty($team))
	{
		$error = $cs_lang['join_denied'];
	}
	else
	{
		$team_id = $team['team_id'];
		$team_name = $team['team_name'];
	}
}

$data['cup']['message'] = $error;
if (isset($_POST['submit']) && empty($error))
{
	/* add team to cup */ 
	$cells = array('cups_id', 'squads_id', 'cupsquads_time');
	$values = array($cups_id, $team['team_id'], cs_time());
	cs_sql_insert(__FILE__,'cupsquads',$cells,$values);

  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear('cups');

  cs_redirect($cs_lang['create_done'],'cups','teams','where='.$cups_id);
}

$data['team']['cups_id'] = $cups_id;
$data['team']['team_id'] = $team_id;
$data['team']['team_name'] = cs_secure($team_name);

echo cs_subtemplate(__FILE__,$data,'cups','teamadd');

?>
