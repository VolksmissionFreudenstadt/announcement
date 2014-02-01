<?php

$img = new Imagick();
$img->newImage (1024, 768, new ImagickPixel('white'));
$img->setImageFormat('jpeg');

// copy test image onto background
$img2 = new Imagick(dirname(__FILE__).'/test/test.jpg');
$img->compositeImage($img2, Imagick::COMPOSITE_DEFAULT, 0, 0);

header('Content-Type: image/jpeg');
echo $img;
