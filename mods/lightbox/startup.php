<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: startup.php 4793 2011-02-09 07:35:37Z hajo $

global $cs_main, $account;


if (!empty($account['access_lightbox']))
{
	if (in_array($cs_main['mod'], array('gallery', 'cups')) && !empty($account['access_'.$cs_main['mod']])) {

		$op_mod = cs_sql_option(__FILE__, $cs_main['mod']);
		if($op_mod['lightbox'] == '1') {
			cs_scriptload('lightbox', 'stylesheet', 'css/slimbox2.css');

			# Slimbox requires jQuery - loaded in cs_template() function
			cs_scriptload('lightbox', 'javascript', 'js/slimbox2.js');
		}
	}
}
