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

require_once 'class.images.helper.php';

$image_source = './images/test.png';

var_dump(ImagesHelper::getimageinfo($image_source));

?> <img src="<?php echo $image_source; ?>"/><?php
# Conver image to grayscale
$grayscale_image = ImagesHelper::colortograyscale($image_source);
ImagesHelper::saveimage($grayscale_image, $image_source,'bw_');

# Conver image to negative
$negative_image  = ImagesHelper::imagetonegative($image_source);
ImagesHelper::saveimage($negative_image, $image_source,'ng_');




?>