-- changes to current database:

ALTER TABLE {pre}_cups ADD cups_access int(2) NOT NULL default '0' AFTER cups_brackets;
ALTER TABLE {pre}_cups ADD cups_checkin int(8) NOT NULL default '0' AFTER cups_start;
ALTER TABLE {pre}_cups ADD cups_notify_via int(4) NOT NULL default 0 AFTER cups_checkin;
ALTER TABLE {pre}_cups ADD cups_notify_hours int(4) NOT NULL default 0 AFTER cups_notify_via;
ALTER TABLE {pre}_cups ADD cups_notified int(2) NOT NULL default 0 AFTER cups_notify_hours;
ALTER TABLE {pre}_cupsquads ADD cupsquads_seed int(5) NOT NULL default 10000 AFTER cupsquads_time;
ALTER TABLE {pre}_cupsquads ADD cupsquads_autoseed int(2) NOT NULL default 1 AFTER cupsquads_seed;
ALTER TABLE {pre}_cupsquads ADD cupsquads_checkedin int(2) NOT NULL default 0 AFTER cupsquads_autoseed;
ALTER TABLE {pre}_cupmatches CHANGE cupmatches_winner cupmatches_winner int(8) NOT NULL default -1;
ALTER TABLE {pre}_cupmatches ADD cupmatches_match int(8) NOT NULL default -1 AFTER cupmatches_tree_order;
ALTER TABLE {pre}_cupmatches ADD cupmatches_nextmatch int(8) NOT NULL default -1 AFTER cupmatches_match;
ALTER TABLE {pre}_cupmatches ADD cupmatches_nextmatchlb int(8) NOT NULL default -1 AFTER cupmatches_nextmatch;
ALTER TABLE {pre}_cupmatches ADD cupmatches_seed1 int(8) NOT NULL default 0 AFTER cupmatches_nextmatchlb;
ALTER TABLE {pre}_cupmatches ADD cupmatches_seed2 int(8) NOT NULL default 0 AFTER cupmatches_seed1;
ALTER TABLE {pre}_cupmatches ADD cupmatches_accepted_time1 int(14) NOT NULL default 0 AFTER cupmatches_accepted1;
ALTER TABLE {pre}_cupmatches ADD cupmatches_accepted_time2 int(14) NOT NULL default 0 AFTER cupmatches_accepted2;
ALTER TABLE {pre}_cupmatches DROP INDEX cups_id;
ALTER TABLE {pre}_cupmatches DROP INDEX cups_id_2;

-- update some stuff
UPDATE {pre}_cups SET cups_checkin = cups_start - 3600 WHERE cups_start > 0;
UPDATE {pre}_cupsquads cs, {pre}_cups cu SET cs.cupsquads_checkedin = 1 WHERE cu.cups_id = cs.cups_id AND cu.cups_checkin < now();

-- cups lightbox
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'lightbox', '1');

-- cups width/height
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'max_navlist', '4');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'max_headline', '20');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'max_gridname', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'html', '1');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'scores', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'width', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'height', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'width_lightbox', '1280');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'title1', 'CLAN');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'title2', 'SPHERE');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_bg', '255,255,255');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_line', '0,0,0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_team_bg', '200,200,200');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_team_bg_lb', '200,100,0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_team_fg', '0,0,0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_headline', '0,0,0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_title1', '186,22,22');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'color_title2', '137,137,137');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'last_check_time', UNIX_TIMESTAMP());
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'notify_hours', 6);
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'notify_email', 1);
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'notify_pm', 1);

