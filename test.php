<?php


function insertScaledText(&$img, $x, $y, $font, $size, $text) {
	// create temp image and insert text
	$tmp = imagecreate(2048,1536);
	$white = imagecolorallocate($tmp, 255, 255, 255);
	$col = imagecolorallocate($tmp, 0, 0, 0);
	imagefill($tmp, $white);
	imagettftext($tmp, $size, 0, 0, 0, $col, $font, $text);
	$dimensions = imagettfbbox($size, 0, $font, $text);
	// scale image down
	$scl = imagescale($tmp, 1024, 768);
	// copy text rectangle onto original image
	imagecopy($img, $scl, $x, $y, 0, 0, $dimensions[2], $dimensions[3]);
	// free memory
	imagedestroy($tmp);
	imagedestroy($scl);
}

$img = @imagecreatetruecolor(1024, 768) or die('Unable to create GD-stream');

// some colors
$color['white'] = imagecolorallocatealpha($img, 255, 255, 255, 127);
$color['black'] = imagecolorallocatealpha($img, 0, 0, 0, 127);

// fill image with white
imagefill($img, 0, 0, $color['white']);

// import title image:
$img2 = imagecreatefromjpeg(dirname(__FILE__).'/test/test.jpg');

// impose on the background image
imagecopy($img, $img2, 0, 0, 0, 0, 1024, 507);

// write first text
//imagettftext ($img, 43, 0, 40, 600, $color['black'], dirname(__FILE__).'/fonts/OpenSans-Regular.ttf', 'Sonntag, 15:00 Uhr');
insertScaledText($img, 40, 600, dirname(__FILE__).'/fonts/OpenSans-Regular.ttf', 43, 'Sonntag, 15:00 Uhr');

// write second text
//imagettftext ($img, 43, 0, 40, 680, $color['black'], dirname(__FILE__).'/fonts/OpenSans-ExtraBold.ttf', "Meet the Pastor\rMeet the Pastor");


imagealphablending($img, true);
imagesavealpha($img, true);

Header('Content-Type: image/jpeg');
imagejpeg($img);

imagedestroy($img);
imagedestroy($img2);
exit;

