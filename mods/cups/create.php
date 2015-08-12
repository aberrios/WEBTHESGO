<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');
$cs_option = cs_sql_option(__FILE__, 'cups');

require_once 'mods/categories/functions.php';
include_once 'mods/cups/defines.php';

$cs_cups = array();
$cs_cups['cups_name'] = empty($_POST['cups_name']) ? '' : $_POST['cups_name'];
$cs_cups['games_id'] = empty($_POST['games_id']) ? 0 : (int) $_POST['games_id'];
$cs_cups['cups_teams'] = empty($_POST['cups_teams']) ? 0 : (int) $_POST['cups_teams'];
$cs_cups['cups_start'] = empty($_POST['cups_start_year']) ? time() + 604800 : cs_datepost('cups_start','unix');
$cs_cups['cups_checkin'] = empty($_POST['cups_checkin_year']) ? time() + 601200 : cs_datepost('cups_checkin','unix');
$cs_cups['cups_system'] = empty($_POST['cups_system']) ? '' : $_POST['cups_system'];
$cs_cups['cups_text'] = empty($_POST['cups_text']) ? '' : $_POST['cups_text'];
$cs_cups['cups_brackets'] = empty($_POST['cups_brackets']) ? CS_CUPS_SYSTEM_KO : (int) $_POST['cups_brackets'];
$cs_cups['cups_access'] = isset($_POST['cups_access']) ? (empty($_POST['cups_access']) ? 0 : (int) $_POST['cups_access']) : 1;

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
  if (substr_count(decbin($cs_cups['cups_teams']),'1') != 1)
    $error .= cs_html_br(1) . $cs_lang['wrong_maxteams'];
}

if(empty($_POST['submit']) || !empty($error)) {
  
  $data = array('cups' => $cs_cups);
  if (!empty($error)) $data['lang']['create_new_cup'] = $cs_lang['error_occured'] . $error;
  
  $cups_start = empty($cs_cups['cups_start']) ? cs_time() : $cs_cups['cups_start'];
  $data['cups']['start'] = cs_dateselect('cups_start', 'unix', $cups_start, 2007);
  $cups_checkin = empty($cs_cups['cups_checkin']) ? cs_time() : $cs_cups['cups_checkin'];
  $data['cups']['checkin'] = cs_dateselect('cups_checkin', 'unix', $cups_checkin, 2007);
  $data['cups']['teams'] = empty($cs_cups['cups_teams']) ? 32 : $cs_cups['cups_teams'];
  
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
  
  echo cs_subtemplate(__FILE__, $data, 'cups', 'create');
  
} else {
  
	$cs_cups['cups_notify_hours'] = $cs_option['notify_hours'];
	$notify = 0;
	if (!empty($cs_option['notify_pm']))
		$notify += constant('CS_CUPS_NOTIFY_PM');
	if (!empty($cs_option['notify_email']))
		$notify += constant('CS_CUPS_NOTIFY_EMAIL');
	$cells = array_keys($cs_cups);
  $values = array_values($cs_cups);
  
  cs_sql_insert(__FILE__,'cups',$cells,$values);
  
  cs_redirect($cs_lang['create_done'],'cups');
  
}
