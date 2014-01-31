<?php

$img = @imagecreatetruecolor(1024, 768) or die('Unable to create GD-stream');

Header('Content-Type: image/jpeg');
imagejpeg($img);

imagedestroy($img);
exit;

