<?php

function getTime($s, $hour=0, $minute=0, $second=0, $base=NULL) {
	if (!is_null($base)) $tmp = strtotime($s, $base); else $tmp = strtotime($s);
	return mktime($hour, $minute, $second, strftime('%m', $tmp), strftime('%d', $tmp), strftime('%Y', $tmp));
}

// global config
define('CONFIG_FILE_NAME', 'config.yaml');
try {
	if (!file_exists(CONFIG_FILE_NAME)) throw new Exception('Konfigurationsdatei '.CONFIG_FILE_NAME.' nicht gefunden.');
	$config = yaml_parse(file_get_contents(CONFIG_FILE_NAME));	
} catch (Exception $e) {}

// get start date:
if (!strftime('%w')) $startDate=getTime('next Sunday'); else $startDate = getTime('now');
$endDate = getTime('+7 days', 23, 59, 59, $startDate);
echo strftime('Start: %d.%m.%Y %H:%M:%S<br />', $startDate);

// connect to db
$db = new mysqli($config['DB']['host'], $config['DB']['user'], $config['DB']['pass'], $config['DB']['name']);
if ($db->connect_errno) {
	raise new Exception('Kann nicht mit der Datenbank verbinden: '.$db->connect_error);
}

$sql = 'SELECT event.*,grp.calendar_id,grp.name FROM ko_event event '
	  .'LEFT JOIN ko_eventgruppen grp ON (event.eventgruppen_id = grp.id) '
	  .'WHERE '
	  .'(STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\')>=\''.strftime('%Y-%m-%d %H:%M:%S', $startDate).'\') '
	  .'AND (STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\')<=\''.strftime('%Y-%m-%d %H:%M:%S', $endDate).'\') '
	  .'AND (grp.calendar_id IN ('.join(',',$config['kOOL']['calendars']).')) '
	  .'ORDER BY STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\') '
	  .';';

die ($sql);
	  