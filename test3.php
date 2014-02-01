<?php

getTime($s, $hour=0, $minute=0, $second=0, $base=NULL) {
	$tmp = strtotime($s, $base);
	return mktime($hour, $minute, $second, strftime('%m', $tmp), strftime('%d', $tmp), strftime('%Y', $tmp));
}

// global config
define('CONFIG_FILE_NAME', 'config.yaml');
try {
	if (!file_exists(CONFIG_FILE_NAME)) throw new Exception('Konfigurationsdatei '.CONFIG_FILE_NAME.' nicht gefunden.');
	$config = yaml_parse(file_get_contents(CONFIG_FILE_NAME));	
} catch (Exception $e) {}

// get start date:
if (!strftime('%w')) $startDate=getTime('next Sunday'); else $startDate = getTime('today');

echo strftime('Start: %d.%m.%Y %H:%M:%S<br />', $startTime);
die ('<pre>'.print_r($config, 1));


// test parameters:
