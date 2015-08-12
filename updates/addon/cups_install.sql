-- access table
ALTER TABLE {pre}_access ADD access_cups int(2) NOT NULL default 0;
UPDATE {pre}_access SET access_cups = 1 WHERE access_id = 1 LIMIT 1;
UPDATE {pre}_access SET access_cups = 2 WHERE access_id = 2 LIMIT 1;
UPDATE {pre}_access SET access_cups = 3 WHERE access_id = 3 LIMIT 1;
UPDATE {pre}_access SET access_cups = 4 WHERE access_id = 4 LIMIT 1;
UPDATE {pre}_access SET access_cups = 5 WHERE access_id = 5 LIMIT 1;

-- the tables
CREATE TABLE {pre}_cups (
  cups_id {serial},
  games_id int(8) NOT NULL default 0,
  cups_name varchar(80) NOT NULL default '',
  cups_system varchar(20) NOT NULL default '',
  cups_text text,
  cups_teams int(4) NOT NULL default 0,
  cups_start int(8) NOT NULL default 0,
  cups_checkin int(8) NOT NULL default 0,
  cups_notify_via int(4) NOT NULL default 0,
  cups_notify_hours int(4) NOT NULL default 0,
  cups_notified int(2) NOT NULL default 0,
  cups_brackets int(2) NOT NULL default 0,
  cups_access int(2) NOT NULL default 0,
  PRIMARY KEY (cups_id)
){engine};

CREATE TABLE {pre}_cupsquads (
  cupsquads_id {serial},
  cups_id int(8) NOT NULL default 0,
  squads_id int(8) NOT NULL default 0,
  cupsquads_time int(14) NOT NULL default 0,
  cupsquads_seed int(5) NOT NULL default 10000,
  cupsquads_autoseed int(2) NOT NULL default 1,
  cupsquads_checkedin int(2) NOT NULL default 0,
  PRIMARY KEY (cupsquads_id),
  UNIQUE (cups_id, squads_id)
){engine};

CREATE TABLE {pre}_cupmatches (
  cupmatches_id {serial},
  cups_id int(8) NOT NULL default 0,
  squad1_id int(8) NOT NULL default 0,
  squad2_id int(8) NOT NULL default 0,
  cupmatches_score1 int(6) NOT NULL default 0,
  cupmatches_score2 int(6) NOT NULL default 0,
  cupmatches_winner int(8) NOT NULL default -1,
  cupmatches_loserbracket int(2) NOT NULL default 0,
  cupmatches_accepted1 int(2) NOT NULL default 0,
  cupmatches_accepted_time1 int(14) NOT NULL default 0,
  cupmatches_accepted2 int(2) NOT NULL default 0,
  cupmatches_accepted_time2 int(14) NOT NULL default 0,
  cupmatches_round int(2) NOT NULL default 0,
  cupmatches_tree_order int(6),
  cupmatches_match int(8) NOT NULL default -1,
  cupmatches_nextmatch int(8) NOT NULL default -1,
  cupmatches_nextmatchlb int(8) NOT NULL default -1,
  cupmatches_seed1 int(8) NOT NULL default 0,
  cupmatches_seed2 int(8) NOT NULL default 0,
  PRIMARY KEY (cupmatches_id)
){engine};

-- the options
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'max_navlist', '4');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'max_headline', '20');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'max_gridname', '0');
INSERT INTO {pre}_options (options_mod, options_name, options_value) VALUES ('cups', 'lightbox', '1');
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

-- the indexes
CREATE INDEX {pre}_cupmatches_cups_id_index ON {pre}_cupmatches (cups_id);
CREATE INDEX {pre}_cupmatches_squad1_id_index ON {pre}_cupmatches (squad1_id);
CREATE INDEX {pre}_cupmatches_squad2_id_index ON {pre}_cupmatches (squad2_id);
CREATE INDEX {pre}_cups_games_id_index ON {pre}_cups (games_id);
CREATE INDEX {pre}_cupsquads_cups_id_index ON {pre}_cupsquads (cups_id);
CREATE INDEX {pre}_cupsquads_squads_id_index ON {pre}_cupsquads (squads_id);
