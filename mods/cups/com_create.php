<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');
$cs_post = cs_post('fid');
$cs_get = cs_get('id');

$fid = empty($cs_post['fid']) ? 0 : $cs_post['fid'];
$quote_id = empty($cs_get['id']) ? 0 : $cs_get['id'];

$tables = 'cupmatches cm LEFT JOIN {pre}_cups cu ON cu.cups_id = cm.cups_id';
$cup = cs_sql_select(__FILE__, $tables, 'cm.cupmatches_id, cm.cups_id, cu.cups_access', 'cm.cupmatches_id = '.$fid); 

if (empty($fid) || empty($cup['cups_access']) || $account['access_cups'] < $cup['cups_access'])
{
  cs_redirect($cs_lang['access_denied'], 'cups', 'list');
}

require_once('mods/comments/functions.php');
cs_commments_create($fid,'cups','match',$quote_id,$cs_lang['mod_name']);