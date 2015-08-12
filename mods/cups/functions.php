<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

/* Cup and grid image generation code:
 *
 * Copyright (c) 2010, Wetzels Holding BV, Remy Wetzels <mindcrime@gab-clan.org>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of Wetzels Holding BV nor the names of its
 *   contributors may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Special notice by Remy Wetzels <mindcrime@gab-clan.org>, September 14, 2010:
 * Permission is hereby granted by Wetzels Holding BV to the ClanSphere Project
 * to omit the above disclaimer in their general documentation and/or
 * ClanSphere about section of the code.
 */

include_once 'mods/cups/defines.php';

function cs_cups_dechex($a)
{
  $n = array();
  $run = 0;
  foreach ($a as $d)
  {
    $dh = dechex($d);
    if (strlen($dh) == 1)
      $dh = '0'.$dh;
    $n[$run++] = $dh;
  }
  return $n;
}

/* make grid line images for HTML grid */
function cs_cups_grid_images()
{
	$options = cs_sql_option(__FILE__,'cups');
	$col = explode(',', $options['color_line']);
  $bgcol = explode(',', $options['color_bg']);
  
	$path = 'uploads/cups/';
	
	/* each grid images consists of 3 tiles: left, center and right */
	$tile_length = floor(CS_CUPS_GRID_IMAGE_WIDTH / 3);
	$tile_center = floor($tile_length / 2) + 1;
	$tile_middle = floor(CS_CUPS_GRID_IMAGE_HEIGHT / 2) + 1;

	/* cup-grid-angle-down */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw horizontal line from left tile to middle */
	imageline($image, 0, $tile_middle, $tile_length + $tile_center, $tile_middle, $col_line);
	/* draw middle down */	
	imageline($image, $tile_length + $tile_center, $tile_middle, $tile_length + $tile_center, CS_CUPS_GRID_IMAGE_HEIGHT, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-angle-down.png');
	
	/* cup-grid-angle-down */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw horizontal line from middle to right */
	imageline($image, $tile_length + $tile_center, $tile_middle, CS_CUPS_GRID_IMAGE_WIDTH, $tile_middle, $col_line);
	/* draw middle down */	
	imageline($image, $tile_length + $tile_center, 0, $tile_length + $tile_center, $tile_middle, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-angle-down-right.png');
	
	/* cup-grid-angle-up */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw horizontal line from left tile to middle */
	imageline($image, 0, $tile_middle, $tile_length + $tile_center, $tile_middle, $col_line);
	/* draw middle down */	
	imageline($image, $tile_length + $tile_center, $tile_middle, $tile_length + $tile_center, 0, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-angle-up.png');

	/* cup-grid-angle-up-right */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw horizontal line from left tile to middle */
	imageline($image, $tile_length + $tile_center, $tile_middle, CS_CUPS_GRID_IMAGE_WIDTH, $tile_middle, $col_line);
	/* draw middle down */	
	imageline($image, $tile_length + $tile_center, $tile_middle, $tile_length + $tile_center, CS_CUPS_GRID_IMAGE_HEIGHT, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-angle-up-right.png');

	/* cup-grid-horizontal */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw vertical down */
	imageline($image, 0, $tile_middle, CS_CUPS_GRID_IMAGE_WIDTH, $tile_middle, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-horizontal.png');

	/* cup-grid-vertical */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw vertical down */
	imageline($image, $tile_length + $tile_center, 0, $tile_length + $tile_center, CS_CUPS_GRID_IMAGE_HEIGHT, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-vertical.png');

	/* cup-grid-vertical-split-right */
	$image = imagecreatetruecolor(CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT);
	$col_line = imagecolorallocate($image, $col[0], $col[1], $col[2]);
	$col_bg = imagecolorallocate($image, $bgcol[0], $bgcol[1], $bgcol[2]);
	/* create transparant background on the basis of the background color */
	imagefilledrectangle($image, 0,0, CS_CUPS_GRID_IMAGE_WIDTH, CS_CUPS_GRID_IMAGE_HEIGHT, $col_bg);
	imagecolortransparent($image, $col_bg);
	/* draw vertical down */
	imageline($image, $tile_length + $tile_center, 0, $tile_length + $tile_center, CS_CUPS_GRID_IMAGE_HEIGHT, $col_line);
	/* draw middle right */	
	imageline($image, $tile_length + $tile_center, $tile_middle, CS_CUPS_GRID_IMAGE_WIDTH, $tile_middle, $col_line);
	/* save */
	imagepng($image, $path.'cup-grid-vertical-split-right.png');
} // function cs_cups_grid_images

function cs_cups_team($cs_lang, $type, $team_id, $team_name, $autoseed, $seed, $html = false, $grid = false)
{
	$options = cs_sql_option(__FILE__,'cups');
	
	if ($grid && ($options['max_gridname'] > 0))
		$team_name = cs_textcut($team_name, $options['max_gridname']);
	$new_name = '';
 	switch ($team_id)
 	{
 	case CS_CUPS_TEAM_UNKNOWN: $new_name = ''; break;
 	case CS_CUPS_TEAM_BYE: $new_name = $cs_lang['bye_grid']; break;
 	default:
    if (empty($team_name) AND !empty($team_id)) $new_name = '? ID:'.$team_id;
    else
    {
	    switch ($type)
	    {
	    case CS_CUPS_TYPE_USERS:
	    	if ($html)
					$new_name = cs_user($team_id, $team_name); // cs_secure in cs_user
				else
					$new_name = cs_secure($team_name);
				break;
	    case CS_CUPS_TYPE_TEAMS:
	    	if ($html)
					$new_name = cs_link(cs_secure($team_name), 'squads', 'view','id='.$team_id);
				else
					$new_name = cs_secure($team_name);
	    	break;
	    }
    }
   	if ($autoseed == 0)
  		$new_name .= ' (#'.$seed.')';
    break;
 	}
 	return $new_name;
} // function cs_cups_team

/* skip some arbitrary precision problem in PHP on FreeBSD 32-bit */
function cs_cups_log($rounds)
{
	$count = 1;

  $r = (float) $rounds / 2.0;
  while ($r > 1.0)
  {
		$count++;
		$r /= 2.0;
  }
//	echo $count.'='.log($rounds,2).'='.intval(log($rounds, 2));
	return $count;
} // function cs_cups_log

function cs_cups_reseed($cups_id)
{
	$cupsquads = cs_sql_select(__FILE__, 'cupsquads', 'cupsquads_id, cupsquads_autoseed', 'cups_id = '.(int) $cups_id, 'cupsquads_autoseed ASC, cupsquads_seed ASC', 0, 0);
	if (!count($cupsquads))
		return;
		
	$seed = 1;
	foreach ($cupsquads as $cupsquad)
	{
		if ($cupsquad['cupsquads_autoseed'] == 1)
		{
			/* finish, rest is autoseed */
			break;
		}
		/* increase seed */
		cs_sql_update(__FILE__, 'cupsquads', array('cupsquads_seed'), array($seed++), $cupsquad['cupsquads_id']);
	}
} // function cs_cups_reseed

function cs_cups_get_teams($cups_id, $cups_type, $cs_lang)
{
	switch ($cups_type)
	{
	case CS_CUPS_TYPE_TEAMS:
		$tables  = 'cupsquads cs LEFT JOIN {pre}_';
		$tables .= 'squads team ON cs.squads_id = team.squads_id';
		$cells   = 'cs.cupsquads_id AS cupsquads_id, cs.cupsquads_time AS cupsquads_time, cs.squads_id AS squads_id, ';
	  $cells  .= 'cs.cupsquads_seed AS cupsquads_seed, cs.cupsquads_autoseed AS cupsquads_autoseed, ';
		$cells  .= 'team.squads_name AS squads_name';
		break;
	case CS_CUPS_TYPE_USERS:
	  $tables  = 'cupsquads cs LEFT JOIN {pre}_';
	  $tables .= 'users team ON cs.squads_id = team.users_id';
	  $cells   = 'cs.cupsquads_id AS cupsquads_id, cs.cupsquads_time AS cupsquads_time, cs.squads_id AS squads_id, ';
	  $cells  .= 'cs.cupsquads_seed AS cupsquads_seed, cs.cupsquads_autoseed AS cupsquads_autoseed, ';
	  $cells  .= 'team.users_nick AS squads_name, team.users_active AS users_active, team.users_delete AS users_delete';
		break;
	}
	
	$teams = cs_sql_select(__FILE__, $tables, $cells, 'cs.cups_id = '.$cups_id, 'cupsquads_autoseed ASC, cupsquads_seed ASC', 0, 0);
	if (!count($teams))
		return NULL;
	foreach ($teams as $key => $team)
	{
		if (empty($team['cupsquads_autoseed']))
		{
			$teams[$key]['seed_text'] = $team['cupsquads_seed'];
			$teams[$key]['autoseed_on'] = '';
			$teams[$key]['autoseed_off'] = 'checked';
		}
		else
		{
			$teams[$key]['seed_text'] = $cs_lang['auto'];
			$teams[$key]['autoseed_on'] = 'checked';
			$teams[$key]['autoseed_off'] = '';
		}
		$teams[$key]['join'] = cs_date('unix', $teams[$key]['cupsquads_time'],1);
		switch ($cups_type)
		{
		case CS_CUPS_TYPE_TEAMS:
			$teams[$key]['link'] = cs_link(cs_secure($team['squads_name']),'squads','view','id=' . $team['squads_id']);
			break;
		case CS_CUPS_TYPE_USERS:
			$teams[$key]['link'] = cs_user($team['squads_id'],$team['squads_name'], $team['users_active'], $team['users_delete']);
			break; 
		}
	}
	return $teams;
} // function cs_cups_get_teams

function cs_cups_generate($seedings, $gridsize, $gridtype)
{ 
	/* init match array */
	$matches = array();
	
	/* first generate the complete winner bracket grid positions */
	$seed = array();
	$grid = array();
	$nextmatch = array();
	$curmatch = array();
	$match = array();
	for ($i = 1; $i <= $gridsize; $i++)
	{
		 $grid[$i] = 0;
		 $seed[$i] = 0;
		 $nextmatch[$i] = 0;
		 $curmatch[$i] = 0;
		 $match[$i] = 0;
	}
	
	/* calculate the seeding positions
	 *
	 * we do this by reverse calculation
	 * starting * from the (expected) winner (seed #1)
	 * 
	 * for each previous round we can say that
	 * the player X who got this far should
	 * have played Y a player with seed position
	 * seed(Y) = gridsize + 1 - seed(X)
	 * in the previous round.
	 *
	 * for each pair of players in the previous
	 * calculation we put the first one on top
	 * and the second on the bottom.
	 * this ensures that seed #1 ends on top
	 * of the final grid, and seed #2 at the
	 * bottom and orders the grid in a nice
	 * fashion.
	 * we do this for each round untill we
	 * reach our final gridsize.
	 *
	 * for example, the calculation goes like this:
	 *
	 * start with gridsize = 1 and seed #1
	 *
	 * previous round had gridsize = 2
	 * we had only seed #1 before, so he played
	 * seed(Y) = 2 + 1 - 1 = 2
	 * so #1 vs #2
	 *
	 * previous round gridsize = 4
	 * we already had seed #1, so #1 played
	 * seed(Y) = 4 + 1 - 1 = 4
	 * so #1 vs #4
	 * we already had seed #2, so #2 played
	 * seed(Y) = 4 + 1 - 2 = 3
	 * so #3 vs #2
	 *
	 * previous round gridsize = 8
	 * we already had seed #1, so #1 played
	 * seed(Y) = 8 + 1 - 1 = 8
	 * so #1 vs #8
	 * we already had seed #4, so #4 played
	 * seed(Y) = 8 + 1 - 4 = 5
	 * so #5 vs #4
	 * we already had seed #3, so #3 played
	 * seed(Y) = 8 + 1 - 3 = 6
	 * so #3 vs #6
	 * we already had seed #2, so #2 played
	 * seed(Y) = 8 + 1 - 2 = 7
	 * so #7 vs #2
	 *
	 * etc...
	 */
	$n = 1; // current gridsize
	$seed[1] = 1; // seed #1 is on this position
	$maxrounds = cs_cups_log($gridsize); // maximum number of wb rounds
	$curround = $maxrounds;
	if ($gridtype == CS_CUPS_SYSTEM_LB)
		$curmatch[1] = 0; // match #0 is grand final
	else
		$curmatch[1] = CS_CUPS_NO_NEXTMATCH; // no match
	while ($n < $gridsize)
	{
		$n *= 2;
		/* make a grid for 1 to $n */
		$top = true; // switch top and lower
		$i = 1; // i goes from 1 - n/2
		$j = 1; // j goes from 1 - n
		while ($j < $n)
		{
			if ($top)
			{
				$nextmatch[$j] = $curmatch[$i]; // next match #
				$match[$i] = $n/2 + $i; // current match # 
				$grid[$j++] = $seed[$i]; // top seed plays
				$grid[$j++] = $n+1-$seed[$i]; // vs lower seed
				$top = false;
			}
			else
			{
				$nextmatch[$j] = $curmatch[$i]; // next match
				$match[$i] = $n/2 + $i; // current match # 
				$grid[$j++] = $n+1-$seed[$i]; // lower seed plays
				$grid[$j++] = $seed[$i]; // vs top seed
				$top = true;
			}
			$i++;
		}
		/* copy current grid array to seed */
		for ($i = 1; $i <= $n; $i++)
		{
			$seed[$i] = $grid[$i];
			$grid[$i] = 0;
			if ($i % 2 == 0)
				$curmatch[$i] = $match[$i/2];
			else 
				$curmatch[$i] = $match[($i+1)/2];
		}
	
//		echo '======= '.$n.' ======<br />';
		/* grid size reached */
		for ($i = 0; $i < $n/2; $i++)
		{
//			echo '---<br />';
//			echo 'round #'.$curround.'<br />';
//			echo 'match #'.$match[$i+1].'<br />';
//			echo '#['.$seed[$i*2+1].']<br />';
//			echo '|<br />';
//			echo '#['.$seed[$i*2+2].']<br />';
//			echo 'winner to match #'.$nextmatch[$i*2+1].'<br />';
//			echo '---<br />';
			$nextmatchlb = CS_CUPS_NO_NEXTMATCH;
			if ($gridtype == CS_CUPS_SYSTEM_KO3RD && $n == 4)
			{
				/* if semi-final in WB and we have a 3rd place match */
				$nextmatchlb = 0;
			}
			if ($n < $gridsize)
			{
				$matches[$match[$i+1]] = array('nextmatch' => $nextmatch[$i*2+1],
																			 'nextmatchlb' => $nextmatchlb,
																			 'loserbracket' => 0,
																			 'round' => $curround,
																			 'squad_id1' => CS_CUPS_TEAM_UNKNOWN,
																			 'squad_id2' => CS_CUPS_TEAM_UNKNOWN,
																			 'tree_order' => $match[$i+1],
																			 'seed1' => $seed[$i*2+1],
																			 'seed2' => $seed[$i*2+2]);
			}
			else
			{
				$matches[$match[$i+1]] = array('nextmatch' => $nextmatch[$i*2+1],
																			 'nextmatchlb' => $nextmatchlb,
																			 'loserbracket' => 0,
																			 'round' => $curround,
																			 'squad_id1' => $seedings[$seed[$i*2+1]],
																			 'squad_id2' => $seedings[$seed[$i*2+2]],
																			 'tree_order' => $match[$i+1],
																			 'seed1' => $seed[$i*2+1],
																			 'seed2' => $seed[$i*2+2]);
			}
		}
		$curround--;
	}
	
	/* we now have all grid positions and matches for the winner bracket and all the teams */
	
	/* add grand final if we have lb */
	if ($gridtype == CS_CUPS_SYSTEM_LB)
	{
		$matches[0] = array('nextmatch' => CS_CUPS_NO_NEXTMATCH,
												'nextmatchlb' => CS_CUPS_NO_NEXTMATCH,
												'loserbracket' => 0,
												'round' => 0,
												'squad_id1' => CS_CUPS_TEAM_UNKNOWN,
												'squad_id2' => CS_CUPS_TEAM_UNKNOWN,
												'tree_order' => 0,
												'seed1' => 1,
												'seed2' => 2);
	}
	/* add 3rd place match */
	else if ($gridtype == CS_CUPS_SYSTEM_KO3RD)
	{
		$matches[0] = array('nextmatch' => CS_CUPS_NO_NEXTMATCH,
												'nextmatchlb' => CS_CUPS_NO_NEXTMATCH,
												'loserbracket' => 0,
												'round' => 0,
												'squad_id1' => CS_CUPS_TEAM_UNKNOWN,
												'squad_id2' => CS_CUPS_TEAM_UNKNOWN,
												'tree_order' => 0,
												'seed1' => 3,
												'seed2' => 4);
	}
	
	/* get LB */
	if ($gridtype == CS_CUPS_SYSTEM_LB)
	{
		$curround = (float) $maxrounds - 0.5;
		for ($i = 1; $i <= $gridsize; $i++)
		{
	  	$grid[$i] = 0;
			$seed[$i] = 0;
			$nextmatch[$i] = 0;
			$match[$i] = 0;
		}
		
		$wbmatches1 = array();
		foreach ($matches as $key => $match)
		{
			if ($match['loserbracket'] == 0
				&& ($match['round'] == 1))
			{
				$wbmatches1[$match['seed1']] = $key;
				$wbmatches1[$match['seed2']] = $key;
			}
		}
		while ($curround >= 1.0)
		{
			$even = true;
			/* uneven round, so losers from WB join the LB */
			if ($curround - floor($curround) == 0.5)
				$even = false;

			/* calculate number of matches in this LB round */
			if ($even)
			{
			  $K = $gridsize / pow(2, $curround);
			  $Z = $K + 1;
				$nummatches = $gridsize / pow(2, (int) $curround+1);
			}
			else
			{
				$nummatches = $gridsize / pow(2, (int) ($curround+0.5)); // K = N / (2 ^ (A+0.5))
				/* in round X+0.5 the losers from the WB X+1 join the LB */
				/* get all matches from WB round X+1 */
				$wbmatches = array();
				$wbround = (int) ($curround + 0.5);
				foreach ($matches as $key => $match)
				{
					if ($wbround == $match['round'] && $match['loserbracket'] == 0)
					{
						/* get probable loser */
						$seed = $match['seed1'] > $match['seed2'] ? $match['seed1'] : $match['seed2'];
						$wbmatches[$seed] = $key;
					}
				}
			} 

			$top = true;
			$i = 1;
			$j = 1;
			while ($i <= $nummatches)
			{
				if ($even)
				{
					$matchnr = $gridsize + $nummatches*3 + $i;
					/* in round X the lower seeds from the WB round X should be here */
					$seedX = $K + $i;
					// X-K + Y-K = Z => Y = Z - X + 2*K
					$seedY = $Z + 2*$K - $seedX;
					/* in the first round we need to point all matches from the wb to this round */
					$matches[$wbmatches1[$seedX]]['nextmatchlb'] = $matchnr;
					$matches[$wbmatches1[$seedY]]['nextmatchlb'] = $matchnr;
					$nextmatch = CS_CUPS_NO_NEXTMATCH;
					foreach ($matches as $key => $match)
					{
						if ($match['loserbracket'] == 1
							&& ($match['round'] == (int) ($curround * 2.0 + 1))
							&& ($match['seed1'] == $seedX || $match['seed2'] == $seedX))
						{
							$nextmatch = $key;
							break;
						}
					}
					$matches[$matchnr] = array('nextmatch' => $nextmatch,
												'nextmatchlb' => CS_CUPS_NO_NEXTMATCH,
												'loserbracket' => 1,
												'round' => (int) ($curround * 2.0),
												'squad_id1' => CS_CUPS_TEAM_UNKNOWN,
												'squad_id2' => CS_CUPS_TEAM_UNKNOWN,
												'tree_order' => 0,
												'seed1' => $seedX,
												'seed2' => $seedY);
				}
				else
				{
					/* in round X+0.5 the losers from the WB X+1 join the LB */
					$matchnr = $gridsize + $nummatches*2 + $i;
					
					/* lb teams should be nummatches + 1 to 2 * nummatches vs 2*nummatches+1 to 3*nummatches */
					$seedX = $nummatches + $i;
					$seedY = 2*$nummatches + $i;
					/* get the wb match which should point to this one, loser joins this match */
					$matches[$wbmatches[$seedX]]['nextmatchlb'] = $matchnr;
					if (intval($curround + 0.5) == $maxrounds)
					{
						/* we are in LB final, next match is grand final */
						$nextmatch = 0;
					}
					else
					{
						/* find the LB match in the next round which has the highest seed in it */
						foreach ($matches as $key => $match)
						{
							if ($match['loserbracket'] == 1
								&& ($match['round'] == (int) ($curround * 2.0 + 1))
								&& ($match['seed1'] == $seedX || $match['seed2'] == $seedX))
							{
								$nextmatch = $key;
								break;
							}
						}
					}
					$matches[$matchnr] = array('nextmatch' => $nextmatch,
												'nextmatchlb' => CS_CUPS_NO_NEXTMATCH,
												'loserbracket' => 1,
												'round' => (int) ($curround * 2.0),
												'squad_id1' => CS_CUPS_TEAM_UNKNOWN,
												'squad_id2' => CS_CUPS_TEAM_UNKNOWN,
												'tree_order' => 0,
												'seed1' => $seedX,
												'seed2' => $seedY);
				}
				$i++;
			}
			$curround -= 0.5;
		}
		
		/* now we generated all the LB matches, order them in the grid */
		$grid = array();
		$curround = (float) $maxrounds - 0.5;
		$nummatches = $gridsize / pow(2, (int) ($curround+0.5)); // K = N / (2 ^ (A+0.5))
		$matchnr = $gridsize + $nummatches*2 + 1;
		/* order the first one */
		$n = $gridsize * 2;
		$matches[$matchnr]['tree_order'] = 1;
		$diff = 1;
		while ($curround > 1.0)
		{
			$even = true;
			/* uneven round, so losers from WB join the LB */
			if ($curround - floor($curround) == 0.5)
			{
				$n /= 2;
				$even = false;
			}
			foreach ($matches as $key => $match)
			{
				if ($match['loserbracket'] == 1 && $match['round'] == (int) ($curround * 2))
				{
					$prev = cs_cups_findprev($matches, $key, 1);
					if (count($prev) == 1)
					{
						/* prev round is an uneven round */
						$nummatches = $gridsize / pow(2, (int) ($curround+0.5)); // K = N / (2 ^ (A+0.5))
						$matches[$prev[0]]['tree_order'] = $match['tree_order'] + $n;
					}
					else
					{
						/* prev round is an even round */
						$matches[$prev[0]]['tree_order'] = $match['tree_order'] - $n + 1;
						$matches[$prev[1]]['tree_order'] = $match['tree_order'] + $n - 1;
					}
				}
			}
			$curround -= 0.5;
			$diff += 2;
		}
	}
	
	return $matches;
}

function cs_cups_findprev($matches, $matchnr, $lb = 1)
{
	$prev = array();
	$run = 0;
	foreach ($matches as $key => $match)
	{
		if ($match['nextmatch'] == $matchnr && $match['loserbracket'] == $lb)
		{
			$prev[$run++] = $key;
		}
	}
	return $prev;
}

/**
 * Recursively close all matches with byes in a cup and update the matches accordingly
 */
function cs_cups_autoclose($cups_id, $level = 1)
{
	$where = 'cups_id = '.(int) $cups_id.' AND '
					.'(squad1_id = '.CS_CUPS_TEAM_BYE.' AND cupmatches_accepted1 = 0) OR '
					.'(squad2_id = '.CS_CUPS_TEAM_BYE.' AND cupmatches_accepted2 = 0)';
	$order = 'cupmatches_loserbracket ASC, cupmatches_round ASC, cupmatches_match ASC';
	$select = cs_sql_select(__FILE__, 'cupmatches', '*', $where, $order, 0, 0);
  if (empty($select))
	{
		/* no more matches, return the number of recursive loops */
		return $level;
	}
	$changed = 0;
	foreach ($select as $match)
	{
		if ($match['squad2_id'] == CS_CUPS_TEAM_BYE)
		{
			if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
			{
				$changed++;
				/* always let first team win if both team are a bye */
				$cells = array('cupmatches_accepted1', 'cupmatches_accepted2', 'cupmatches_winner', 'cupmatches_score1', 'cupmatches_score2');
				$values = array(1, 1, $match['squad1_id'], 1, 0);
				cs_sql_update(__FILE__, 'cupmatches', $cells, $values, $match['cupmatches_id']);
				if ($match['cupmatches_nextmatch'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add winner team (coming from match in lb/wb) to the next wb/lb match */
					cs_cups_addteam2match($cups_id, $match['squad1_id'], $match['cupmatches_match'], $match['cupmatches_round'], $match['cupmatches_loserbracket'], $match['cupmatches_nextmatch'], true); 
				}
				if ($match['cupmatches_nextmatchlb'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add loser team to the next lb match */
					cs_cups_addteam2match($cups_id, $match['squad2_id'], $match['cupmatches_match'], $match['cupmatches_round'], $match['cupmatches_loserbracket'], $match['cupmatches_nextmatchlb'], true); 
				}
			}
		}
		else /* first team must be a bye */
		{
			if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
			{
				$changed++;
				$cells = array('cupmatches_accepted1', 'cupmatches_accepted2', 'cupmatches_winner', 'cupmatches_score1', 'cupmatches_score2');
				$values = array(1, 1, $match['squad2_id'], 0, 1);
				cs_sql_update(__FILE__, 'cupmatches', $cells, $values, $match['cupmatches_id']);
				if ($match['cupmatches_nextmatch'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add winner team (coming from match in lb/wb) to the next wb/lb match */
					cs_cups_addteam2match($cups_id, $match['squad2_id'], $match['cupmatches_match'], $match['cupmatches_round'], $match['cupmatches_loserbracket'], $match['cupmatches_nextmatch'], true); 
				}
				if ($match['cupmatches_nextmatchlb'] != CS_CUPS_NO_NEXTMATCH)
				{
					/* add loser team to the next lb match */
					cs_cups_addteam2match($cups_id, $match['squad1_id'], $match['cupmatches_match'], $match['cupmatches_round'], $match['cupmatches_loserbracket'], $match['cupmatches_nextmatchlb'], true); 
				}
			}
		}
	}

	if ($changed == 0)
	{
		if (function_exists('cs_datacache_load'))
			cs_datacache_clear('cups');

		return $level;
	}
	return cs_cups_autoclose($cups_id, $level + 1);
}

function cs_cups_autofix($cups_id, $level = 1)
{
	// todo
	return $level;
}

function cs_cups_addteam2match($cups_id, $teamid, $prevmatchid, $prevmatchround, $prevmatchlb, $matchid, $is_admin)
{
	$return = true;
	$match = cs_sql_select(__FILE__, 'cupmatches', '*', 'cups_id = '.$cups_id.' AND cupmatches_match = '.$matchid, 0, 0, 1);
	if (empty($match['cupmatches_id']))
	{
		cs_error(__FILE__, 'ERROR: unknown match #'.$matchid.' for cup #'.$cups_id, 1);
		return false;
	}
	/* get the other match which is referencing to this match */
	$othermatch = cs_sql_select(__FILE__, 'cupmatches', '*', 'cups_id = '.$cups_id.' AND (cupmatches_nextmatch = '.$matchid.' OR cupmatches_nextmatchlb = '.$matchid.') AND cupmatches_match <> '.$prevmatchid, 0, 0, 1);
	if (empty($match['cupmatches_id']))
	{
		cs_error(__FILE__, 'ERROR: unknown other match for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
		return false;
	}
	/* determine on which position this team has to be put */
	if ($match['cupmatches_loserbracket'] == 0) /* this match is in the winner bracket */
	{
		if ($match['cupmatches_match'] != 0)
		{
			/* this match is in the winner bracket, so both previous matches should be from the same round  */
			if ($prevmatchround != $othermatch['cupmatches_round'])
			{
				cs_error(__FILE__, 'ERROR: other match for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid.' not in the same round', 1);
				$return = false;
			}
			/* place the team according to match number */
			if ($prevmatchid < $othermatch['cupmatches_match'])
			{
				/* place on position 1 */
				if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
				{
					if ($is_admin)
						cs_error(__FILE__, 'WARNING WB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					else
						cs_error(__FILE__, 'ERROR WB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					$return = false;
				}
				$cells = array('squad1_id');
			}
			else
			{
				/* place on position 2 */
				if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
				{
					if ($is_admin)
						cs_error(__FILE__, 'WARNING WB: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					else
						cs_error(__FILE__, 'ERROR WB: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					$return = false;
				}
				$cells = array('squad2_id');
			}
		}
		else
		{
			/* grand final or 3rd place match */
			if ($prevmatchlb == 1 || $othermatch['cupmatches_loserbracket'] == 1)
			{
				/* grand final, since one of them comes from a loserbracket */
				if ($prevmatchlb == 0)
				{
					/* team coming from wb */
					/* place on position 1 */
					if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
					{
						if ($is_admin)
							cs_error(__FILE__, 'WARNING WB GF: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						else
							cs_error(__FILE__, 'ERROR WB GF: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						$return = false;
					}
					$cells = array('squad1_id');
				}
				else
				{
					/* team coming from lb */
					/* place on position 2 */
					if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
					{
						if ($is_admin)
							cs_error(__FILE__, 'WARNING WB GF: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						else
							cs_error(__FILE__, 'ERROR WB GF: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						$return = false;
					}
					$cells = array('squad2_id');
				}
			}
			else
			{
				/* 3rd place match */
				/* place the team according to match number */
				if ($prevmatchid < $othermatch['cupmatches_match'])
				{
					/* place on position 1 */
					if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
					{
						if ($is_admin)
							cs_error(__FILE__, 'WARNING WB 3RD: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						else
							cs_error(__FILE__, 'ERROR WB 3RD: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						$return = false;
					}
					$cells = array('squad1_id');
				}
				else
				{
					/* place on position 2 */
					if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
					{
						if ($is_admin)
							cs_error(__FILE__, 'WARNING WB 3RD: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						else
							cs_error(__FILE__, 'ERROR WB 3RD: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						$return = false;
					}
					$cells = array('squad2_id');
				}
			}
		}
	}
	else /* is loserbracket match */
	{
		if ($prevmatchlb == 0 || $othermatch['cupmatches_loserbracket'] == 0)
		{
			/* one of them comes from the winner bracket, so it's a half round match */
			if ($prevmatchlb == 0)
			{
				/* team comes from wb */
				if ($othermatch['cupmatches_loserbracket'] == 0)
				{
					/* both come from wb (first round LB) */
					if ($prevmatchid < $othermatch['cupmatches_match'])
					{
						/* place on position 1 */
						if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
						{
							if ($is_admin)
								cs_error(__FILE__, 'WARNING LB 1RD: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
							else
								cs_error(__FILE__, 'ERROR LB 1RD: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
							$return = false;
						}
						$cells = array('squad1_id');
					}
					else
					{
						/* place on position 2 */
						if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
						{
							if ($is_admin)
								cs_error(__FILE__, 'WARNING LB 1RD: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
							else
								cs_error(__FILE__, 'ERROR LB 1RD: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
							$return = false;
						}
						$cells = array('squad2_id');
					}
				}
				else
				{
					/* this team comes from wb, place on position 1 */
					if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
					{
						if ($is_admin)
							cs_error(__FILE__, 'WARNING LB FWB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						else
							cs_error(__FILE__, 'ERROR LB FWB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
						$return = false;
					}
					$cells = array('squad1_id');
				}
			}
			else
			{
				/* team comes from lb */
				/* place on position 2 */
				if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
				{
					if ($is_admin)
						cs_error(__FILE__, 'WARNING LB FWB: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					else
						cs_error(__FILE__, 'ERROR LB FWB: position2 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					$return = false;
				}
				$cells = array('squad2_id');
			}
		}
		else
		{
			/* both of them coming from LB */
			/* place the team according to match number */
			if ($prevmatchid < $othermatch['cupmatches_match'])
			{
				/* place on position 1 */
				if ($match['squad1_id'] != CS_CUPS_TEAM_UNKNOWN)
				{
					if ($is_admin)
						cs_error(__FILE__, 'WARNIGN LB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					else
						cs_error(__FILE__, 'ERROR LB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					$return = false;
				}
				$cells = array('squad1_id');
			}
			else
			{
				/* place on position 2 */
				if ($match['squad2_id'] != CS_CUPS_TEAM_UNKNOWN)
				{
					if ($is_admin)
						cs_error(__FILE__, 'WARNING LB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					else
						cs_error(__FILE__, 'ERROR LB: position1 in use other for match #'.$matchid.' for cup #'.$cups_id.' and prevmatch #'.$prevmatchid, 1);
					$return = false;
				}
				$cells = array('squad2_id');
			}
		}
	}
	/* we have determined the position */
	$values = array($teamid);
	cs_sql_update(__FILE__, 'cupmatches', $cells, $values, $match['cupmatches_id']);

	if (function_exists('cs_datacache_load'))
		cs_datacache_clear('cups');

	/* if there were any admin matchedits, fix tree recursively */
	cs_cups_autofix($cups_id);
	return $return;
}
?>
