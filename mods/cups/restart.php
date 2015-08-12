<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: remove.php 4603 2010-10-19 16:42:28Z Fr33z3m4n $

$cs_lang = cs_translate('cups');
$cs_get = cs_get('id');
$cs_post = cs_post('id');
$cups_id = empty($cs_get['id']) ? $cs_post['id'] : $cs_get['id'];

if(isset($_POST['submit'])) {
  cs_sql_delete(__FILE__,'cupmatches',$cups_id,'cups_id');

  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear('cups');

  cs_redirect('', 'cups');
}

if (isset($_POST['cancel'])) {
  cs_redirect($cs_lang['canceled'], 'cups');
}

if(!isset($_POST['submit'])) {
  $cup = cs_sql_select(__FILE__,'cups','cups_name','cups_id = ' . $cups_id,0,0,1);
  if(!empty($cup)) {
    $data = array();
    $data['cup']['id'] = $cups_id;
    echo cs_subtemplate(__FILE__, $data, 'cups', 'restart');
  } else {
    cs_redirect('','cups');
  }
}
