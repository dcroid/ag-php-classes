<?php
/**
 * @revision      $Id$   
 * @created       Apr 22, 2011
 * @category      category
 * @package       package
 * @version       version
 * @copyright     Copyright Alexey Gordeyev IK � 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once 'class.images.reflection_2.php';

$path_to_source = 'http://agjoomla.com/media/k2/categories/2.png';
// $path_to_target = 'example.jpg';

$REFLC = new Images_Reflection($path_to_source);
$REFLC->setBackgoundColor('#ccc');
$REFLC->saveImage();
// $REFLC->showReflection();
$REFLC->buildReflection();
// $REFLC->buildFade();
$REFLC->buildImageWithReflection();
$REFLC->saveImage();
$REFLC->showImage(false);


// var_dump($REFLC->getImageInfo());
// $REFLC->setSourceImagePath($path_to_source);
// $REFLC->setTargetImagePath($path_to_target);
// $REFLC->loadLocalImage();
// $REFLC->createReflection();
// $REFLC->createImageWithReflection();
// $REFLC->saveImage($path_to_target);

?>
