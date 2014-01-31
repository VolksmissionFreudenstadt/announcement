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
imagettftext ($img, 43, 0, 40, 600, $color['black'], dirname(__FILE__).'/fonts/OpenSans-Regular.ttf', 'Sonntag, 15:00 Uhr');

// write second text
imagettftext ($img, 43, 0, 40, 680, $color['black'], dirname(__FILE__).'/fonts/OpenSans-ExtraBold.ttf', "Meet the Pastor\rMeet the Pastor");


imagealphablending($img, true);
imagesavealpha($img, true);

Header('Content-Type: image/jpeg');
imagejpeg($img);

imagedestroy($img);
imagedestroy($img2);
exit;

