<?php
// ClanSphere 2009 - www.clansphere.net
// $Id$
$cs_lang = cs_translate('cups');

include_once 'mods/cups/functions.php';

if(isset($_POST['submit'])) {
  
  $save = array();
  $save['width'] = (int) $_POST['width'];
  $save['height'] = (int) $_POST['height'];
  $save['width_lightbox'] = (int) $_POST['width_lightbox'];
  $save['scores'] = !empty($_POST['scores']) ? 1 : 0;
  $save['html'] = !empty($_POST['html']) ? 1 : 0;
  $save['title1'] = $_POST['title1'];
  $save['title2'] = $_POST['title2'];
  $save['lightbox'] = (int) $_POST['lightbox'];
  $save['max_navlist'] = (int) $_POST['max_navlist'];
  $save['max_headline'] = (int) $_POST['max_headline'];
  $save['max_gridname'] = (int) $_POST['max_gridname'];
  $save['notify_hours'] = $_POST['notify_hours'] > 0 ? (int) $_POST['notify_hours'] : 0;
  $save['notify_pm'] = !empty($_POST['notify_pm']) ? 1 : 0;
	$save['notify_email'] = !empty($_POST['notify_email']) ? 1 : 0;
	
	$color_bg = !empty($_POST['color_bg']) ? explode(',', $_POST['color_bg']) : array(255,255,255);
	if (count($color_bg) != 3)
		$color_bg = array(255, 255, 255);
	$color_line = !empty($_POST['color_line']) ? explode(',', $_POST['color_line']) : array(0,0,0);
	if (count($color_line) != 3)
		$color_line = array(0, 0, 0);
	$color_team_bg = !empty($_POST['color_team_bg']) ? explode(',', $_POST['color_team_bg']) : array(255,255,255);
	if (count($color_team_bg) != 3)
		$color_team_bg = array(255, 255, 255);
	$color_team_bg_lb = !empty($_POST['color_team_bg_lb']) ? explode(',', $_POST['color_team_bg_lb']) : array(255,255,255);
	if (count($color_team_bg_lb) != 3)
		$color_team_bg_lb = array(255, 255, 255);
	$color_team_fg = !empty($_POST['color_team_fg']) ? explode(',', $_POST['color_team_fg']) : array(0,0,0);
	if (count($color_team_fg) != 3)
		$color_team_fg = array(0, 0, 0);
	$color_headline = !empty($_POST['color_headline']) ? explode(',', $_POST['color_headline']) : array(0,0,0);
	if (count($color_headline) != 3)
		$color_headline = array(0, 0, 0);
	$color_title1 = !empty($_POST['color_title1']) ? explode(',', $_POST['color_title1']) : array(0,0,0);
	if (count($color_title1) != 3)
		$color_title1 = array(0, 0, 0);
	$color_title2 = !empty($_POST['color_title2']) ? explode(',', $_POST['color_title2']) : array(0,0,0);
	if (count($color_title2) != 3)
		$color_title2 = array(0, 0, 0);
  $save['color_bg'] = implode(',', $color_bg);
  $save['color_line'] = implode(',', $color_line);
  $save['color_team_bg'] = implode(',', $color_team_bg);
  $save['color_team_bg_lb'] = implode(',', $color_team_bg_lb);
  $save['color_team_fg'] = implode(',', $color_team_fg);
  $save['color_headline'] = implode(',', $color_headline);
  $save['color_title1'] = implode(',', $color_title1);
  $save['color_title2'] = implode(',', $color_title2);

  require_once 'mods/clansphere/func_options.php';
  cs_optionsave('cups', $save);

	// create/replace grid images
	cs_cups_grid_images();
	
  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear('cups');

  cs_redirect($cs_lang['changes_done'], 'options', 'roots');
  
} else {
  
  $data = array();
  $data['com'] = cs_sql_option(__FILE__,'cups');

	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_bg']));
	$data['com']['sample_bg'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_line']));
	$data['com']['sample_line'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_team_bg']));
	$data['com']['sample_team_bg'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_team_bg_lb']));
	$data['com']['sample_team_bg_lb'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_team_fg']));
	$data['com']['sample_team_fg'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_headline']));
	$data['com']['sample_headline'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_title1']));
	$data['com']['sample_title1'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';
	$s_bg = cs_cups_dechex(explode(',', $data['com']['color_title2']));
	$data['com']['sample_title2'] = '<span style="color: #'.$s_bg[0].$s_bg[1].$s_bg[2].'; font-weight: bold;">'.$cs_lang['example'].'</span>';

	$data['com']['scores_yes'] = $data['com']['scores'] == 1 ? 'checked' : '';
	$data['com']['scores_no'] = $data['com']['scores'] == 1 ? '' : 'checked';
	$data['com']['html_yes'] = $data['com']['html'] == 1 ? 'checked' : '';
	$data['com']['html_no'] = $data['com']['html'] == 1 ? '' : 'checked';
	$data['com']['notify_pm'] = $data['com']['notify_pm'] == 1 ? 'checked' : '';
	$data['com']['notify_email'] = $data['com']['notify_email'] == 1 ? 'checked' : '';
	
	/* lightbox */
	$levels = 0;
	$var = '';
	while($levels < 2) {
	  $data['com']['lightbox'] == $levels ? $sel = 1 : $sel = 0;
	  $var .= cs_html_option($cs_lang['light_' . $levels],$levels,$sel);
	  $levels++;
	}
	$data['lightbox']['options'] = $var;

  echo cs_subtemplate(__FILE__,$data,'cups','options');
}

?>
