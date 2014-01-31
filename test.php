<?php

$img = @imagecreatetruecolor(1024, 768) or die('Unable to create GD-stream');

// some colors
$color['white'] = imagecolorallocatealpha($img, 255, 255, 255, 127);

// fill image with white
imagefill($img, 0, 0, $color['white']);

// import title image:
$img2 = imagecreatefromjpeg(dirname(__FILE__).'/test/test.jpg');

// impose on the background image
imagecopy($img, $img2, 0, 0, 0, 0, 1024, 507);



Header('Content-Type: image/jpeg');
imagejpeg($img);

imagedestroy($img);
imagedestroy($img2);
exit;

