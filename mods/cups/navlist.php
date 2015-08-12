<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: navlist.php 4819 2011-03-01 22:27:33Z hajo $

$data = array();
$cs_option = cs_sql_option(__FILE__,'cups');
$cells = 'cups_id, cups_name, cups_start';
$where = 'cups_access <= '.$account['access_cups'].' AND cups_access <> 0';
$data['cups'] = cs_sql_select(__FILE__,'cups',$cells,$where,'cups_start DESC',0,$cs_option['max_navlist']);
$count_cups = count($data['cups']);

for ($i = 0; $i < $count_cups; $i++) {
  $data['cups'][$i]['view_url'] = cs_url('cups','view','id=' . $data['cups'][$i]['cups_id']);
  if (strlen($data['cups'][$i]['cups_name']) > $cs_option['max_headline'])
  	$data['cups'][$i]['cups_name'] = cs_textcut($data['cups'][$i]['cups_name'], $options['max_headline']);
  $data['cups'][$i]['cups_start'] = cs_date('unix',$data['cups'][$i]['cups_start']);
}

echo cs_subtemplate(__FILE__,$data,'cups','navlist');
