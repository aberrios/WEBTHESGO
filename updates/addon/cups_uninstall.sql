DROP TABLE {pre}_cups;
DROP TABLE {pre}_cupsquads;
DROP TABLE {pre}_cupmatches;

DELETE FROM {pre}_options WHERE options_mod = 'cups';

ALTER TABLE {pre}_access DROP 'access_cups';
