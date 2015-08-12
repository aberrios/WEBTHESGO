<?php
// ClanSphere 2010 - www.clansphere.net
// $Id: teamremove.php 4494 2010-08-29 18:52:01Z Spongebob $

$cs_lang = cs_translate('cups');
$data = array();

include_once 'mods/cups/functions.php';

$teams_id = (int) $_GET['id'];

if (isset($_GET['cancel'])) {
  $cs_team = cs_sql_select(__FILE__,'cupsquads','cups_id','cupsquads_id = ' . $teams_id);

  cs_redirect($cs_lang['del_false'],'cups','teams','where=' . $cs_team['cups_id']);
} elseif (isset($_GET['confirm']) && $teams_id > CS_CUPS_TEAM_BYE) {
  $cs_team = cs_sql_select(__FILE__,'cupsquads','cups_id, squads_id','cupsquads_id = ' . $teams_id);
  $cs_cup = cs_sql_select(__FILE__,'cups','cups_system, cups_name','cups_id = ' . $cs_team['cups_id']);

	/* get the open match if any */
	$cs_match = cs_sql_select(__FILE__,'cupmatches', '*', '(squad1_id = '.$cs_team['squads_id'].' OR squad2_id = '.$cs_team['squads_id'].') AND cups_id = '.$cs_team['cups_id'].' AND cupmatches_winner = '.CS_CUPS_TEAM_UNKNOWN);
	if (!empty($cs_match['cupmatches_id'])) 
	{
		$cells = array('squad1_id', 'squad2_id', 'cupmatches_score1', 'cupmatches_score2', 'cupmatches_accepted1', 'cupmatches_accepted2');
		/* set this team as a bye, let the autoclose code handle the rest */
		if ($cs_match['squad1_id'] == $cs_team['squads_id'])
			$values = array(CS_CUPS_TEAM_BYE, $cs_match['squad2_id'], 0, 0, 0, 0);
		else
			$values = array($cs_match['squad1_id'], CS_CUPS_TEAM_BYE, 0, 0, 0, 0);
		cs_sql_update(__FILE__, 'cupmatches', $cells, $values, $cs_match['cupmatches_id']);
		/* autoclose the match(es) */
		cs_cups_autoclose($cs_team['cups_id']);
	}

  cs_sql_delete(__FILE__,'cupsquads',$teams_id);

  $messages_cells = array('users_id','messages_time','messages_subject','messages_text',
        'users_id_to','messages_show_receiver');
  $messages_text = html_entity_decode(sprintf($cs_lang['team_removed_mail'],$cs_cup['cups_name']), ENT_QUOTES, $cs_main['charset']);
  if ($cs_cup['cups_system'] == CS_CUPS_TYPE_USERS) {
    $messages_values = array($account['users_id'],cs_time(),$cs_lang['team_removed'],$messages_text,
      $cs_team['squads_id'],'1');
    cs_sql_insert(__FILE__,'messages',$messages_cells,$messages_values);
  } else {
    $cs_members = cs_sql_select(__FILE__,'members','users_id','squads_id = ' . $teams_id,0,0,0);
    $count_members = count($cs_members);
    for ($run = 0; $run < $count_members; $run++) {
      $messages_values = array($account['users_id'],cs_time(),$cs_lang['team_removed'],$messages_text,
        $cs_members[$run]['users_id'],'1');
      cs_sql_insert(__FILE__,'messages',$messages_cells,$messages_values);
    }
  }

  // clear datacache
	if (function_exists('cs_datacache_load'))
		cs_datacache_clear('cups');

  cs_redirect($cs_lang['del_true'] . '. ' . $cs_lang['sent_message'],'cups','teams','where='.$cs_team['cups_id']);

} else {
  $data['head']['topline']  = $cs_lang['rly_remove_participant'];
  $data['cups']['content']  = cs_link($cs_lang['confirm'],'cups','teamremove','id='.$teams_id.'&amp;confirm');
  $data['cups']['content'] .= ' - ';
  $data['cups']['content'] .= cs_link($cs_lang['cancel'],'cups','teamremove','id='.$teams_id.'&amp;cancel');
}

echo cs_subtemplate(__FILE__,$data,'cups','teamremove');
