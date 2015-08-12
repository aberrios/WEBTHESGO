<?php
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

require_once('mods/cups/defines.php');
$cs_lang = cs_translate('cups');

$cups_id = empty($_GET['id']) ? 0 : (int) $_GET['id'];

if ($cups_id <= 0)
{
	cs_redirect($cs_lang['no_data'], 'cups', 'list');
	return;
}

$cup = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = '.$cups_id, 0, 0, 1);
if (empty($cup))
{
	cs_redirect($cs_lang['no_data'], 'cups', 'list');
	return;
}

$time = cs_time();
if ($cup['cups_checkin'] > $time)
{
	echo sprintf($cs_lang['checkin_notyet'], date('Y-m-d @H:i', $cup['cups_checkin']));
	return;
}
if ($time > $cup['cups_start'])
{
	echo $cs_lang['cup_started'];
	return;
}

$checked_in = cs_sql_count(__FILE__, 'cupsquads', 'cupsquads_checkedin = 1 AND cups_id = '.$cups_id);
$full = empty($_GET['checkout']) && $checked_in >= $cup['cups_teams'] ? 1 : 0;

if (!empty($full))
{
	cs_redirect($cs_lang['already_full'], 'cups', 'view', 'id='.$cups_id);
	return;
}

$membership = 0;
if ($cup['cups_system'] == CS_CUPS_TYPE_TEAMS)
{
	$membership = cs_sql_count(__FILE__,'members','users_id = ' . $account['users_id'] . ' AND members_admin = 1');
	if (!empty($membership))
	{
		$tables = 'cupsquads cs INNER JOIN {pre}_members mem ON cs.squads_id = mem.squads_id';
		$where = 'mem.users_id = ' . $account['users_id'] . ' AND mem.members_admin = 1 AND cs.cups_id = ' . $cups_id;
		$participant = cs_sql_select(__FILE__,$tables, 'cs.cupsquads_id, cs.cupsquads_checkedin', $where, 0, 0, 1);
	}
}
else
{
	$membership = 1;
	$participant = cs_sql_select(__FILE__,'cupsquads', 'cupsquads_id, cupsquads_checkedin', 'cups_id = ' . $cups_id . ' AND squads_id = ' . $account['users_id'], 0, 0, 1);
}

if (empty($membership) || empty($participant))
{
	cs_redirect($cs_lang['no_data'], 'cups', 'view', 'id='.$cups_id);
	return;
}

if (!empty($participant['cupsquads_checkedin']) && !empty($_GET['checkout']))
{
	cs_sql_update(__FILE__, 'cupsquads', array('cupsquads_checkedin'), array(0), $participant['cupsquads_id']);
}
else if (empty($participant['cupsquads_checkedin']) && empty($_GET['checkout']))
{
	cs_sql_update(__FILE__, 'cupsquads', array('cupsquads_checkedin'), array(1), $participant['cupsquads_id']);
}

cs_redirect('', 'cups', 'view', 'id='.$cups_id);

?>
