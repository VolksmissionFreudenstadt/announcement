<?php

$img = new Imagick();
$img->newImage (1024, 768, new ImagickPixel('white'));
$img->setImageFormat('jpeg');

// copy test image onto background
$img2 = new Imagick(dirname(__FILE__).'/test/test.jpg');
$img->compositeImage($img2, Imagick::COMPOSITE_DEFAULT, 0, 0);

// font settings
$draw = new ImagickDraw();
$draw->setFillColor('black');
$draw->setFont(dirname(__FILE__).'/fonts/OpenSans-Regular.ttf');
$draw->setFontSize(43);

// first text
$img->annotateImage($draw, 40, 600, 0, 'Sonntag, 15:00 Uhr');

// second text
$draw->setFont(dirname(__FILE__).'/fonts/OpenSans-ExtraBold.ttf');
$draw->setFontSize(50);
$img->annotateImage($draw, 40, 680, 0, 'Meet the Pastor');


header('Content-Type: image/jpeg');
echo $img;
