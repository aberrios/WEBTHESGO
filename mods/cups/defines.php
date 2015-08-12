<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

define('CS_CUPS_GRID_IMAGE_HEIGHT', 19); // should be an uneven number to get the line in the middle
define('CS_CUPS_GRID_IMAGE_WIDTH', 15); // must be a factor of 3 of an uneven number (3 * 3, 3 * 5, 3 * 7, ...)

define('CS_CUPS_GRID_SCORE_WIDTH', 20); // width of the score column
 
define('CS_CUPS_MAX_SEED', 9999);

define('CS_CUPS_TYPE_TEAMS', 'teams');
define('CS_CUPS_TYPE_USERS', 'users');

define('CS_CUPS_SYSTEM_KO', 0); // KO system
define('CS_CUPS_SYSTEM_LB', 1); // WB, LB and Grand Final
define('CS_CUPS_SYSTEM_KO3RD', 2); // KO system with 3rd place 

define('CS_CUPS_TEAM_UNKNOWN', -1); 
define('CS_CUPS_TEAM_BYE', 0);

define('CS_CUPS_NO_NEXTMATCH', -1);

define('CS_CUPS_NOTIFY_PM', 1);
define('CS_CUPS_NOTIFY_EMAIL', 2);

define('CS_CUPS_GD_CHARSET', 'ISO-8859-1');
?>
