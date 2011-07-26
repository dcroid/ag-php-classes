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
$width = ImagesHelper::getimageinfo($image_source,'width');
# get image height
$height = ImagesHelper::getimageinfo($image_source,'height');
# get image mime/type
$mime = ImagesHelper::getimageinfo($image_source,'mime');

# Conver image to grayscale
if($grayscale_image = ImagesHelper::colortograyscale($image_source)) {
   # save grayscale image in to file as gif
   ImagesHelper::saveimage($grayscale_image, $image_source,'bw_','gif');
}

# Conver image to negative
if($negative_image  = ImagesHelper::imagetonegative($image_source)) {
   # save image negative in to file as png
   ImagesHelper::saveimage($negative_image, $image_source,'bw_','png');
}

// ImagesHelper::showimage($grayscale_image,'png',100);


?>