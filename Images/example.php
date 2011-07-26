<?php
/**
 * @revision      $Id$
 * @created       Jul 22, 2011
 * @package       Images
 * @subpackage	  Helper
 * @category      Utillites
 * @version       1.0.0
 * @copyright     Copyright Alexey Gordeyev IK © 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'class.images.helper.php';

$image_source = './images/test.png';
$new_image    = 'test.png';

# get full information from image 
$image_info = ImagesHelper::getimageinfo($image_source);

# get image width
$width      = ImagesHelper::getimageinfo($image_source,'width');
# or
$width      = ImagesHelper::width($image_source);

# get image height
$height     = ImagesHelper::getimageinfo($image_source,'height');
# or 
$height     = ImagesHelper::height($image_source);

# get image mime/type
$mime       = ImagesHelper::getimageinfo($image_source,'mime');
# or
$mime       = ImagesHelper::mime($image_source);


# Convert hex color to rgb
$hex_color = '#cccccc';
$rgb_color = ImagesHelper::hextorgb($hex_color);
// var_dump($rgb_color);

# Conver image to grayscale
if($grayscale_image = ImagesHelper::colortograyscale($image_source)) {
   # save grayscale image in to file as gif
   ImagesHelper::saveimage($grayscale_image, $image_source,'bw_','gif');
}

# Conver image to negative
if($negative_image  = ImagesHelper::imagetonegative($image_source)) {
   # save image negative in to file as png
   ImagesHelper::saveimage($negative_image, $image_source,'ng_','png');
}

# Resize image example 1
$new_width = 50; // new image width
if($resized_image  = ImagesHelper::resizeimage($image_source,$new_width)) {
   # save thumblain image in to file as jpg
   ImagesHelper::saveimage($resized_image, $image_source,'thumb_','jpg');   
}

# Resize image example 2
$resize_to = 'height';
$new_value = 30; // new height value
if($resized_image  = ImagesHelper::resizeimage($image_source,$new_value,$resize_to)) {
   # save thumblain image in to file as jpg
   ImagesHelper::saveimage($resized_image, $image_source,'thumb_','gif');   
}

# Resize image example 3
$width = 90;
$height = 30;
if($fix_resized_image  = ImagesHelper::resize($image_source,$width,$height)) {
   # save resized image with fixed size in to file as jpg
   ImagesHelper::saveimage($fix_resized_image, $image_source,'fix_','gif');   
}

# Scale image
$ratio = 70;
if($scaled_image  = ImagesHelper::scale($image_source,$ratio)) {
   # save scaled image in to file as jpg
   ImagesHelper::saveimage($scaled_image, $image_source,'sca_','gif');   
}


# build reflection 
if($image_reflection  = ImagesHelper::buildreflection($image_source,25)) {
   # save reflection image in to file as gif
   ImagesHelper::saveimage($image_reflection, $image_source,'ref_','gif');  
}

# build image with reflection
if($image_with_reflection  = ImagesHelper::buildimagewithreflection($image_source)) {
   # save resulted image in to file as png
   ImagesHelper::saveimage($image_with_reflection, $image_source,'wref_','png');   
}


?>