<?php

$img = @imagecreatetruecolor(1024, 768) or die('Unable to create GD-stream');

// some colors
$color['white'] = imagecolorallocatealpha($img, 255, 255, 255, 127);

// fill image with white
imagefill($img, 0, 0, $color['white']);

Header('Content-Type: image/jpeg');
imagejpeg($img);

imagedestroy($img);
exit;

