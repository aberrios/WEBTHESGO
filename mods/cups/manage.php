<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

$cs_lang = cs_translate('cups');

include_once 'mods/cups/defines.php';

$data = array();
$data['vars']['count'] = cs_sql_count(__FILE__,'cups');
$data['vars']['message'] = cs_getmsg();

$cells = 'cups_id, games_id, cups_name, cups_start, cups_teams, cups_system, cups_brackets';
$data['cups'] = cs_sql_select(__FILE__,'cups',$cells,'','cups_start ASC',0,0);
$count_cups = count($data['cups']);

$img_start = cs_icon('forward');
$img_restart = cs_icon('reload');

for ($i = 0; $i < $count_cups; $i++) {  
  $data['cups'][$i]['participations'] = cs_sql_count(__FILE__, 'cupsquads', 'cups_id = ' . $data['cups'][$i]['cups_id']);  
  $data['cups'][$i]['checkedin'] = cs_sql_count(__FILE__, 'cupsquads', 'cupsquads_checkedin = 1 AND cups_id = ' . $data['cups'][$i]['cups_id']);  
      
	$system = ($data['cups'][$i]['cups_system'] == CS_CUPS_TYPE_TEAMS ? $cs_lang['teams'] : $cs_lang['users'] );
	switch($data['cups'][$i]['cups_brackets'])
	{
		default: break;
		case CS_CUPS_SYSTEM_KO: $system .= ' ('.$cs_lang['brackets_ko'].')'; break;
		case CS_CUPS_SYSTEM_KO3RD: $system .= ' ('.$cs_lang['brackets_ko3rd'].')'; break;
		case CS_CUPS_SYSTEM_LB: $system .= ' ('.$cs_lang['brackets_de'].')'; break;
	}
	$data['cups'][$i]['cups_system'] = $system;
  if(file_exists('uploads/games/' . $data['cups'][$i]['games_id'] . '.gif')) {
    $data['cups'][$i]['game'] = cs_html_img('uploads/games/' . $data['cups'][$i]['games_id'] . '.gif');
  } else {
    $data['cups'][$i]['game'] = '';
  }
  
  $where = "games_id = '" . $data['cups'][$i]['games_id'] . "'";
  $cs_game = cs_sql_select(__FILE__,'games','games_name, games_id',$where);
  $id = 'id=' . $cs_game['games_id'];
  $data['cups'][$i]['game'] .= ' ' . cs_link($cs_game['games_name'],'games','view',$id);  
  
  $matchcount = cs_sql_count(__FILE__,'cupmatches','cups_id = ' . $data['cups'][$i]['cups_id']);
  $data['cups'][$i]['if']['start'] = empty($matchcount) && $data['cups'][$i]['cups_start'] < cs_time() ? true : false;
  $data['cups'][$i]['if']['restart'] = empty($matchcount) ? false : true;
  $data['cups'][$i]['if']['seed'] = empty($matchcount) ? true : false;
  $data['cups'][$i]['start_link'] = cs_link($img_start,'cups','start','id=' . $data['cups'][$i]['cups_id'], 0, $cs_lang['start']);
  $data['cups'][$i]['restart_link'] = cs_link($img_restart,'cups','restart','id=' . $data['cups'][$i]['cups_id'], 0, $cs_lang['restart']);
  
}

echo cs_subtemplate(__FILE__, $data, 'cups', 'manage');
