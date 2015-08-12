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

$cupsquads_id = (empty($_GET['cupsquads_id']) ? 0 : (int) $_GET['cupsquads_id']);
$cups_id = (empty($_GET['cups_id']) ? 0 : (int) $_GET['cups_id']);

if ($cupsquads_id > 0 && $cups_id > 0)
{
	$value = 1;
	if (!empty($_GET['checkout']))
		$value = 0;
	cs_sql_update(__FILE__, 'cupsquads', array('cupsquads_checkedin'), array($value), $cupsquads_id);
}
cs_redirect('', 'cups','teams','where='.$cups_id);
?>
