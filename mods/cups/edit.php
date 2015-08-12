<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

require_once 'mods/categories/functions.php';
include_once 'mods/cups/defines.php';

$cs_cups = array();
$cs_cups['cups_name'] = empty($_POST['cups_name']) ? '' : $_POST['cups_name'];
$cs_cups['games_id'] = empty($_POST['games_id']) ? 0 : (int) $_POST['games_id'];
$cs_cups['cups_teams'] = empty($_POST['cups_teams']) ? 0 : (int) $_POST['cups_teams'];
$cs_cups['cups_start'] = cs_datepost('cups_start','unix');
$cs_cups['cups_checkin'] = cs_datepost('cups_checkin','unix');
$cs_cups['cups_system'] = empty($_POST['cups_system']) ? '' : $_POST['cups_system'];
$cs_cups['cups_text'] = empty($_POST['cups_text']) ? '' : $_POST['cups_text'];
$cs_cups['cups_brackets'] = empty($_POST['cups_brackets']) ? CS_CUPS_SYSTEM_KO : (int) $_POST['cups_brackets'];
$cs_cups['cups_access'] = empty($_POST['cups_access']) ? 0 : (int) $_POST['cups_access'];
$cs_cups['cups_notified'] = empty($_POST['cups_notified']) ? 0 : 1;
$cs_cups['cups_notify_hours'] = empty($_POST['cups_notify_hours']) ? 0 : (int) $_POST['cups_notify_hours'];
if ($cs_cups['cups_notify_hours'] < 0)
	$cs_cups['cups_notify_hours'] = 0;
$cs_cups['cups_notify_via'] = 0;
if (!empty($_POST['cups_notify_pm']))
	$cs_cups['cups_notify_via'] += constant('CS_CUPS_NOTIFY_PM');
if (!empty($_POST['cups_notify_email']))
	$cs_cups['cups_notify_via'] += constant('CS_CUPS_NOTIFY_EMAIL');


if (isset($_POST['submit'])) {
  
  $error = '';
  
  if ($cs_cups['cups_start'] <= $cs_cups['cups_checkin'] + 900)
    $error .= cs_html_br(1) . $cs_lang['checkin_before_start'];
	if (empty($cs_cups['cups_name']))
    $error .= cs_html_br(1) . $cs_lang['no_name'];
  if (empty($cs_cups['games_id']))
    $error .= cs_html_br(1) . $cs_lang['no_game'];
  if (empty($cs_cups['cups_teams']))
    $error .= cs_html_br(1) . $cs_lang['no_maxteams'];
  elseif (substr_count(decbin($cs_cups['cups_teams']),1) != 1)
    $error .= cs_html_br(1) . $cs_lang['wrong_maxteams'];
}

if (empty($_POST['submit']) || !empty($error)) {
  
  $cups_id = empty($error) ? (int) $_GET['id'] : (int) $_POST['cups_id'];
  
  if (empty($error)) {
    $cells = 'cups_id, games_id, cups_name, cups_system, cups_text, cups_teams, cups_start, cups_checkin, cups_brackets, cups_access, cups_notified, cups_notify_via, cups_notify_hours';
    $cs_cups = cs_sql_select(__FILE__,'cups',$cells,'cups_id=\''.$cups_id.'\'');
  }
  
  $data = array('cups' => $cs_cups);
  
  if (!empty($error)) $data['lang']['edit_cup'] = $cs_lang['error_occured'] . $error;
  
  $cups_start = empty($cs_cups['cups_start']) ? cs_time() : $cs_cups['cups_start'];
  $data['cups']['start'] = cs_dateselect('cups_start', 'unix', $cups_start, 2007);
  $cups_checkin = empty($cs_cups['cups_checkin']) ? cs_time() : $cs_cups['cups_checkin'];
  $data['cups']['checkin'] = cs_dateselect('cups_checkin', 'unix', $cups_checkin, 2007);
  $data['cups']['teams'] = !isset($cs_cups['cups_teams']) ? 32 : $cs_cups['cups_teams'];
  $data['cups']['cups_id'] = $cups_id;
  $data['cups']['cups_notified_checked'] = empty($cs_cups['cups_notified']) ? '' : 'checked';
  $data['cups']['cups_notify_pm_checked'] = (($cs_cups['cups_notify_via'] & constant('CS_CUPS_NOTIFY_PM')) == constant('CS_CUPS_NOTIFY_PM')) ? 'checked' : '';
  $data['cups']['cups_notify_email_checked'] = (($cs_cups['cups_notify_via'] & constant('CS_CUPS_NOTIFY_EMAIL')) == constant('CS_CUPS_NOTIFY_EMAIL')) ? 'checked' : '';
  $data['cups']['cups_notify_hours'] = $cs_cups['cups_notify_hours'];
  
  $cups_system = empty($cs_cups['cups_system']) ? CS_CUPS_TYPE_TEAMS : $cs_cups['cups_system'];
  $data['sel']['teams'] = $cups_system == CS_CUPS_TYPE_TEAMS ? ' selected="selected"' : '';
  $data['sel']['users'] = $cups_system == CS_CUPS_TYPE_USERS ? ' selected="selected"' : '';
  $data['sel']['ko'] = $cs_cups['cups_brackets'] == CS_CUPS_SYSTEM_KO ? ' selected="selected"' : '';
  $data['sel']['ko3rd'] = $cs_cups['cups_brackets'] == CS_CUPS_SYSTEM_KO3RD ? ' selected="selected"' : '';
  $data['sel']['brackets'] = $cs_cups['cups_brackets'] == CS_CUPS_SYSTEM_LB ? ' selected="selected"' : '';
  
  $data['games'] = cs_sql_select(__FILE__,'games','games_name,games_id',0,'games_name',0,0);
  $games_count = count($data['games']);
  
  $levels = 0;
  $sel = 0;
  while($levels < 6) {
    $cs_cups['cups_access'] == $levels ? $sel = 1 : $sel = 0;
    $data['access'][$levels]['sel'] = cs_html_option($levels . ' - ' . $cs_lang['lev_' . $levels],$levels,$sel);
    $levels++;
  }

  if (!empty($cs_cups['games_id']))
    for ($i = 0; $i < $games_count; $i++)
      $data['games'][$i]['selected'] = $data['games'][$i]['games_id'] == $cs_cups['games_id'] ? 'selected="selected"' : '';  
  
  echo cs_subtemplate(__FILE__, $data, 'cups', 'edit');
  
} else {
  
  $cups_id = (int) $_POST['cups_id'];
  
  $cells = array_keys($cs_cups);
  $values = array_values($cs_cups);
  
  cs_sql_update(__FILE__,'cups',$cells,$values,$cups_id);
  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear('cups');
  
  cs_redirect($cs_lang['changes_done'], 'cups') ;
  
}
