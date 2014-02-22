<html>
	<head>
		<title>Bekanntmachungen erstellen</title>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.10.4/themes/humanity/jquery-ui.css" />
		 <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		 <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	</head>
	<body>
<?php


function createImage ($event, $index, $config, $startDate, $endDate) {
	// format title
	$gConf = $config['groups'][$event['name']];
	$titleParts = array();
	if (!$gConf['no_group']) $titleParts[] = $event['name'];
	if (!$gConf['no_title']) $titleParts[] = $event['my_vmfds_events_announcement_title'] ? $event['my_vmfds_events_announcement_title'] : ($event['title'] ? $event['title'] : $event['kommentar']);
	$title = join(': ', $titleParts);
	
	// format time:
	$eStart = strtotime($event['startdatum']);
	if ($eStart>$endDate) {
		$timeInfo = strftime('%A, %d.%m., ', $eStart).substr($event['startzeit'], 0, 5).' Uhr';
	} else {
		$timeInfo = strftime('%A, ', $eStart).substr($event['startzeit'], 0, 5).' Uhr';
	}
	
	// create the image
	$img = new Imagick();
	$img->newImage (1024, 768, new ImagickPixel('white'));
	$img->setImageFormat('jpeg');
	
	// copy test image onto background
	$img2 = new Imagick(dirname(__FILE__).'/'.$event['image']);
	$img->compositeImage($img2, Imagick::COMPOSITE_DEFAULT, 0, 0);
	
	// font settings
	$draw = new ImagickDraw();
	$draw->setFillColor('black');
	$draw->setFont(dirname(__FILE__).'/fonts/OpenSans-Regular.ttf');
	$draw->setFontSize(43);
	
	// first text
	$img->annotateImage($draw, 30, 580, 0, $timeInfo);
	
	// second text
	$draw->setFont(dirname(__FILE__).'/fonts/OpenSans-ExtraBold.ttf');
	$draw->setFontSize(60);
	$img->annotateImage($draw, 30, 650, 0, $title);
	
	// third text?
	if ($event['my_vmfds_events_announcement_note'] && (!$gConf['no_note'])) {
		$draw->setFont(dirname(__FILE__).'/fonts/OpenSans-Italic.ttf');
		$draw->setFontSize(38);
		$img->annotateImage($draw, 30, 710, 0, $event['my_vmfds_events_announcement_note']);
	}
	
	
	
	// write the image to the output folder
	$fileBaseName = $config['output']['prefix'].str_pad($index, 3, '0', STR_PAD_LEFT).'.jpg';
	$fileName = $config['output']['path'].'/'.$fileBaseName;
	echo 'Erstelle Folie für "'.utf8_encode($title).'" als '.$fileName.' ...<br />';
	$img->writeImage($fileName);
	
	return $fileBaseName;
}


function getTime($s, $hour=0, $minute=0, $second=0, $base=NULL) {
	if (!is_null($base)) $tmp = strtotime($s, $base); else $tmp = strtotime($s);
	return mktime($hour, $minute, $second, strftime('%m', $tmp), strftime('%d', $tmp), strftime('%Y', $tmp));
}

define('DEBUG', $_POST['debug']);
if (DEBUG) echo '<pre>'.print_r($_POST).'</pre>';

// global config
define('CONFIG_FILE_NAME', 'config.yaml');
try {
	if (!file_exists(CONFIG_FILE_NAME)) throw new Exception('Konfigurationsdatei '.CONFIG_FILE_NAME.' nicht gefunden.');
	$config = yaml_parse(file_get_contents(CONFIG_FILE_NAME));	
} catch (Exception $e) {}

// get start date:
if (strftime('%w')) $startDate=getTime('next Sunday', 11); else $startDate = getTime('now', 11);
$endDate = getTime('+7 days', 23, 59, 59, $startDate);

if (DEBUG) echo strftime('%d.%m.%Y %H:%M%S', $startDate).' - '.strftime('%d.%m.%Y %H:%M%S', $endDate);

// time code in file names
$config['output']['prefix'] = strftime($config['output']['prefix'], $startDate);

// connect to db
$db = new mysqli($config['DB']['host'], $config['DB']['user'], $config['DB']['pass'], $config['DB']['name']);
if ($db->connect_errno) {
	throw new Exception('Kann nicht mit der Datenbank verbinden: '.$db->connect_error);
}

$sql = 'SELECT event.*,grp.my_vmfds_events_announcement_group_image,grp.calendar_id,grp.name FROM ko_event event '
	  .'LEFT JOIN ko_eventgruppen grp ON (event.eventgruppen_id = grp.id) '
	  .'WHERE '
	  .'(((STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\')>=\''.strftime('%Y-%m-%d %H:%M:%S', $startDate).'\') '
	  .'AND (STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\')<=\''.strftime('%Y-%m-%d %H:%M:%S', $endDate).'\')) '
	  .' OR (event.my_vmfds_events_announcement_start <= \''.strftime('%Y-%m-%d', $startDate).'\'))'
	  .'AND (grp.calendar_id IN ('.join(',',$config['kOOL']['calendars']).')) '
	  .'ORDER BY STR_TO_DATE(CONCAT(event.startdatum, \' \', event.startzeit), \'%Y-%m-%d %H:%i:%s\') '
	  .';';

if (DEBUG) echo $sql.'<br />';

$res = $db->query($sql);
$rows = array();
while ($row = $res->fetch_assoc()) $rows[] = $row;

foreach ($rows as $key => $row)
	$rows[$key]['image'] = $row['my_vmfds_events_announcement_image'] ? $row['my_vmfds_events_announcement_image'] : $row['my_vmfds_events_announcement_group_image'];

setlocale(LC_ALL, $config['locale']);

// remove all files from output folder first?
if ($config['output']['clear']) {
	$files = glob($config['output']['path'].'/*'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file))
			unlink($file); // delete file
	}	
}

$index = 0;
$presentationFiles = array();

// copy "pre" files
if ($config['include']['pre'] && $_POST['pre']) {
	$files = glob($config['include']['pre'].'/*'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file)) {
			$index++;
			$destBase = $config['output']['prefix'].str_pad($index, 3, '0', STR_PAD_LEFT).'.'.pathinfo($file, PATHINFO_EXTENSION);
			$dest = $config['output']['path'].'/'.$destBase;
			$presentationFiles[] = $destBase;
			echo 'Kopiere '.$file.' nach '.$dest.'...<br />';
			copy($file, $dest);
		}
	}	
}

// output the images
if ($_POST['events']) {
	foreach ($rows as $event) {
		$index++;
		$presentationFiles[] = createImage($event, $index, $config, $startDate, $endDate);
	}
}


// copy "post" files
if ($config['include']['post'] && $_POST['post']) {
	$files = glob($config['include']['post'].'/*'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file)) {
			$destBase = $config['output']['prefix'].str_pad($index, 3, '0', STR_PAD_LEFT).'.'.pathinfo($file, PATHINFO_EXTENSION);
			$dest = $config['output']['path'].'/'.$destBase;
			$presentationFiles[] = $destBase;
			echo 'Kopiere '.$file.' nach '.$dest.'...<br />';
			copy($file, $dest);
		}
	}
}


// create presentation
define ('CRLF', "\r\n");
$presentation = strftime($config['presentation']['path'], $startDate);
echo 'Saving SongBeamer slideshow '.$presentation.' ... <br />';
$fp = fopen ($presentation, 'w');
fwrite ($fp, 'object PresentationSlideShow: TPresentationSlideShow'.CRLF);
fwrite ($fp, '  SlideCollection = <');

foreach ($presentationFiles as $img) {
	fwrite ($fp, CRLF.'    item'.CRLF);
	fwrite ($fp, '      FileName = \''.$config['presentation']['base'].$img.'\''.CRLF);
	fwrite ($fp, '    end');
	
}

fwrite ($fp, '>'.CRLF.'  Loop = True'.CRLF);
fwrite ($fp, '  FitToScreen = True'.CRLF);
fwrite ($fp, 'end');
fclose ($fp);


echo '<hr /><a href="index.php">Zurück zum Formular</a>';