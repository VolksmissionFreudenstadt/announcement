<?php

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
imagettftext ($img, 43, 0, 40, 580, $color['black'], dirname(__FILE__).'/fonts/OpenSans-Regular.ttf', 'Sonntag, 15:00 Uhr');

// write second text
imagettftext ($img, 43, 0, 40, 700, $color['black'], dirname(__FILE__).'/fonts/OpenSans-ExtraBold.ttf', 'Meet the Pastor');


Header('Content-Type: image/jpeg');
imagejpeg($img);

imagedestroy($img);
imagedestroy($img2);
exit;

