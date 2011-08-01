<?php
/**
 * @revision      $Id$
 * @created       Jul 22, 2011
 * @package       Images
 * @subpackage	  Helper
 * @category      Utillites
 * @version       1.0.0
 * @desc          Example using ImagesHelper class
 * @copyright     Copyright Alexey Gordeyev IK © 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 * @source        http://code.google.com/p/ag-php-classes/
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'class.images.helper.php';
/*
echo ' GIF: '.IMAGETYPE_GIF;
echo ' JPEG: '.IMAGETYPE_JPEG;
echo ' PNG: '.IMAGETYPE_PNG;
echo ' BMP: '.IMAGETYPE_BMP;
echo ' XBM: '.IMAGETYPE_XBM;
echo ' WBMP: '.IMAGETYPE_WBMP;

Your username is : bereg.muzeum.net.ua@email.ua
Your password is : CTT63573
*/


// $image_source = './images/source.jpg';
$image_source = ImagesHelper::downloadimage('http://mp3passion.net/uploads/posts/thumbs/1240391573_nice_lounge_part12_500.jpg','./images/');
$new_image    = 'test.png';
// $image_source = './images/source.jpg';

# get full information from image 
$image_info = ImagesHelper::getimageinfo($image_source);
// var_dump($image_info);
/*
if(is_object($image_source)) {
   echo "Is object";
} else {
   $image = ImagesHelper::createimagefromsource($image_source);
   if(is_object($image)) {
      echo "Cool is object";
   } else {
      echo gettype($image) . "\n";
      echo get_resource_type($image) . "\n";
   }
}
*/

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

# Convert image format
ImagesHelper::convert($image_source,'png');
ImagesHelper::convert($image_source,'wbmp');
ImagesHelper::convert($image_source,'xbm');

# Convert image to grayscale
if($grayscale_image = ImagesHelper::colortograyscale($image_source)) {
   # save grayscale image in to file in gif format
   ImagesHelper::saveimage($grayscale_image, $image_source,'bw_','gif');
   $grayscale_image  = ImagesHelper::buildimagewithreflection($grayscale_image);
   ImagesHelper::showimage($grayscale_image,'png',100);
}

# Convert image to grayscale
if($grayscale_image = ImagesHelper::colortograyscale($image_source)) {
   $grayscale_image  = ImagesHelper::imagetonegative($grayscale_image);
   # save grayscale image in to file in gif format
   ImagesHelper::saveimage($grayscale_image, $image_source,'bw2_','gif');
}

# Convert image to negative
if($negative_image  = ImagesHelper::imagetonegative($image_source)) {
   # save image negative in to file in png format
   ImagesHelper::saveimage($negative_image, $image_source,'ng_','png');
}

# Resize image example 1
$new_width = 50; // new image width px
if($resized_image  = ImagesHelper::resizeimage($image_source,$new_width)) {
   # save thumblain image in to file in jpg format
   ImagesHelper::saveimage($resized_image, $image_source,'thumb_','jpg');   
}

# Resize image example 2
$resize_to = 'height';
$new_value = 30; // new height value px
if($resized_image  = ImagesHelper::resizeimage($image_source,$new_value,$resize_to)) {
   # save thumblain image in to file in gif format
   ImagesHelper::saveimage($resized_image, $image_source,'thumb_','gif');   
}

# Resize image example 3
$width  = 90; # px
$height = 30; # px
if($fix_resized_image  = ImagesHelper::resize($image_source,$width,$height)) {
   # save resized image with fixed size in to file in gif format
   ImagesHelper::saveimage($fix_resized_image, $image_source,'fix_','gif');   
}

# Scale image
$ratio = 70; # % 
if($scaled_image  = ImagesHelper::scale($image_source,$ratio)) {
   # save scaled image in to file in gif format
   ImagesHelper::saveimage($scaled_image, $image_source,'sca_','gif');   
}

if($quadrate_image  = ImagesHelper::quadrate($image_source,$ratio)) { 
   # save scaled image in to file in gif format
   ImagesHelper::saveimage($quadrate_image, $image_source,'qua_','gif'); 
}

# build reflection 
$reflection_height = 25; # % 
if($image_reflection  = ImagesHelper::buildreflection($image_source,$reflection_height)) {
   # save reflection image in to file in gif format
   ImagesHelper::saveimage($image_reflection, $image_source,'ref_','png');  
}

# build image with reflection
if($image_with_reflection  = ImagesHelper::buildimagewithreflection($image_source)) {
   # save resulted image in to file in png format
   ImagesHelper::saveimage($image_with_reflection, $image_source,'wref_','png');   
}

# build faded image
$alpha_start = 120;
$alpha_end = 0;
$bg_color = "#000"; //   #7E58BF
if($faded_image  = ImagesHelper::fade($image_source,$alpha_start,$alpha_end,$bg_color)) {
   # save resulted image in to file in png format
   ImagesHelper::saveimage($faded_image, $image_source,'fad2_','png');  
}

# rotate image example 1 with transparent background
$degrees = 55;
if($rotated_image  = ImagesHelper::rotate($image_source,$degrees)) {
   # save rotated image in to file in png format
   ImagesHelper::saveimage($rotated_image, $image_source,'rtdt_','png');    
}

# rotate image example 2 with filled background 
$bg_color = '#7E58BF';
$transparent = false;
if($rotated_image  = ImagesHelper::rotate($image_source,$degrees,$bg_color,$transparent)) {
   # save rotated image in to file in png format
   ImagesHelper::saveimage($rotated_image, $image_source,'rtdf_','png');    
}

# build text watemark
$text = 'agjoomla';
$font = './fonts/ithornet.ttf';
if($watermarked_image  = ImagesHelper::buildtextwatermark( $image_source, $text, $font)) {
   # save watermarked image in to file in jpg format
   ImagesHelper::saveimage($watermarked_image, $image_source,'wtmt_','jpg'); 
}

# build image watemark
$watermark_img = './images/wtm.png';
if($watermarked_image = ImagesHelper::buildimagewatermark($image_source, $watermark_img)) {
   # save watermarked image in to file in gif format
   ImagesHelper::saveimage($watermarked_image, $image_source,'wtmi_','gif');    
}

/*
'horizontal'
'vertical'
'ellipse'
'ellipse2'
'circle'
'circle2'
'square'
'rectangle'
'diamond'
*/   
$width = 200;
$height = 100;
$direction = 'ellipse';
$start_color = '#5E0B5F';
$end_color = '#fff';
$step = 0;
if($gradient_image = ImagesHelper::gradientfill($width,$height,$direction,$start_color,$end_color,$step)) {
   # save watermarked image in to file in gif format
   ImagesHelper::saveimage($gradient_image, $image_source,'gr1_','png');   
}

$width = 100;
$direction = 'circle';
if($gradient_image = ImagesHelper::gradientfill($width,$height,$direction,$start_color,$end_color,$step)) {
   # save watermarked image in to file in gif format
   ImagesHelper::saveimage($gradient_image, $image_source,'gr2_','png');   
}

$direction = 'horizontal';
if($gradient_image = ImagesHelper::gradientfill($width,$height,$direction,$start_color,$end_color,$step)) {
   # save watermarked image in to file in gif format
   ImagesHelper::saveimage($gradient_image, $image_source,'gr3_','png');   
}

$direction = 'rectangle';
if($gradient_image = ImagesHelper::gradientfill($width,$height,$direction,$start_color,$end_color,$step)) {
   # save watermarked image in to file in gif format
   ImagesHelper::saveimage($gradient_image, $image_source,'gr4_','png');   
}

$direction = 'square';
if($gradient_image = ImagesHelper::gradientfill($width,$height,$direction,$start_color,$end_color,$step)) {
   # build transparent
   $gradient_image = ImagesHelper::tranparent($gradient_image,$start_color);
   # save watermarked image in to file in gif format
   ImagesHelper::saveimage($gradient_image, $image_source,'gr5_','png');   
}
?>