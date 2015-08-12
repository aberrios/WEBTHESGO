<?php

$cup_time = cs_time();
$cs_options = cs_sql_option(__FILE__, 'cups');

/* check only every hour */ 
if ($cs_options['last_check_time'] < $cup_time - 3600)
{
	/* update checktime first, to prevent duplicate runs as much as possible */
	require_once('mods/clansphere/func_options.php');
	cs_optionsave('cups', array('last_check_time' => $cup_time));
	
	$cups = cs_sql_select(__FILE__, 'cups', '*', 'cups_notified = 0', 0, 0, 0);
	
	$langs = array();
	if (count($cups) > 0)
	foreach ($cups as $cup)
	{
		if ($cup['cups_start'] >= $cup_time)
		{
			if ($cup['cups_notify_via'] == 0)
			{
				/* do not notify anybody, this might be changed at a later date, so ignore
				 * until the cup has started (cups_start will set notified)
				 */
				continue;
			}
			if ($cup['cups_checkin'] - 3600*$cup['cups_notify_hours'] > $cup_time)
			{
				/* we do not need to notify anybody yet */
				continue;
			}
			/* ok, apparently we need to notify someone, get all users */
			include_once 'mods/cups/defines.php';
			$squads = cs_sql_select(__FILE__,'cupsquads', 'squads_id', 'cups_id = ' . $cup['cups_id'], 0, 0, 0);
			$users = array();
			if ($cup['cups_system'] == CS_CUPS_TYPE_TEAMS)
			{
				/* get the member admins of the squads */
				$squad_ids = array();
				foreach ($squads as $squad)
				{
					$squad_ids[] = $squad['squads_id'];
				}
				$member_admins = cs_sql_select(__FILE__,'members', 'users_id',
						'squads_id IN ('.implode(',', $squad_ids).') AND members_admin = 1', 0, 0, 0);
				if (count($member_admins))
				foreach ($member_admin as $member)
				{
					$users[] = (int) $member['users_id'];
				}
			}
			else
			{
				foreach ($squads as $squad)
				{
					$users[] = (int) $squad['squads_id'];
				}
			}
			/* squads might have multiple member admins who might be in multiple squads */
			// $users = array_unique($users, SORT_NUMERIC);
			$users = array_unique($users);
			if (count($users) > 0)
			{
				$userdata = cs_sql_select(__FILE__, 'users', 'users_email, users_nick, users_lang, users_id, users_dstime, users_dstime, users_timezone',
							'users_active = 1 AND users_delete = 0 AND users_id IN ('.implode(',', $users).')', 0, 0, 0);
				if (count($userdata))
				foreach ($userdata as $user)
				{
					/* timezone and dst stuff */
					$cup_start = $cup['cups_start'] + $user['users_timezone'];
					$cup_checkin = $cup['cups_checkin'] + $user['users_timezone'];
					if (empty($user['users_dstime']) AND date('I',$cup_start) != '0' OR $user['users_dstime'] == 'on')
					{
						$cup_start += 3600;
						$cup_checkin += 3600;
					}
					
					if (!file_exists('lang/'.$user['users_lang'].'/system/main.php'))
						$user['users_lang'] = $cs_main['def_lang'];
					if (!isset($langs[$user['users_lang']]))
					{
				    require 'lang/'.$user['users_lang'].'/system/main.php';
				    $lang_main = $cs_lang;
				    $cs_lang = array();
				    require 'lang/'.$user['users_lang'].'/cups.php';
				    $langs[$user['users_lang']] = array_merge($lang_main, $cs_lang);
					}
					$cs_lang = $langs[$user['users_lang']];
					$search = array('{cups_url}',
													'{cups_checkin}',
													'{cups_start}',
													'{cups_name}',
													'{users_nick}');
					$replace = array($cs_main['php_self']['website'].html_entity_decode(cs_url('cups','checkin','id='.$cup['cups_id']), ENT_QUOTES, $cs_main['charset']),
													date('Y-m-d @H:i', $cup_checkin),
													date('Y-m-d @H:i', $cup_start),
													$cup['cups_name'],
													$user['users_nick']);
					$cup_text = str_replace($search, $replace, html_entity_decode($cs_lang['notify_text'], ENT_QUOTES, $cs_main['charset']));
					/* now send the data via PM */
					if (($cup['cups_notify_via'] & constant('CS_CUPS_NOTIFY_PM')) == constant('CS_CUPS_NOTIFY_PM'))
					{
						$messages_cells = array('users_id','messages_time','messages_subject','messages_text',
				      'users_id_to','messages_show_receiver','messages_show_sender');
				    $messages_save = array(1,$cup_time,$cs_lang['notify_subject'],$cup_text,$user['users_id'],
				      1,0);
				    cs_sql_insert(__FILE__,'messages',$messages_cells,$messages_save);
					}
					$search = array('{cups_url}',
													'{cups_checkin}',
													'{cups_start}',
													'{cups_name}',
													'{users_nick}');
					$replace = array($cs_main['php_self']['website'].html_entity_decode(cs_url('cups','checkin','id='.$cup['cups_id']), ENT_QUOTES, $cs_main['charset']),
													date('Y-m-d @H:i', $cup_checkin),
													date('Y-m-d @H:i', $cup_start),
													$cup['cups_name'],
													$user['users_nick']);
					$cup_text = str_replace($search, $replace, html_entity_decode($cs_lang['notify_text'], ENT_QUOTES, $cs_main['charset']));
					/* now send the data via e-mail */
					if (($cup['cups_notify_via'] & constant('CS_CUPS_NOTIFY_EMAIL')) == constant('CS_CUPS_NOTIFY_EMAIL'))
					{
						cs_mail($user['users_email'],$cs_lang['notify_subject'],$cup_text);
					}
				}
			}
		}
		/* else, already started, only update cups_notified */
		cs_sql_update(__FILE__, 'cups', array('cups_notified'), array(1), $cup['cups_id']);
	}
}
