<?php

$img = new Imagick();
$img->newImage (1024, 768, new ImagickPixel('white'));
$img->setImageFormat('jpeg');


header('Content-Type: image/jpeg');
echo $img;
