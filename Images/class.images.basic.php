<?php
/**
 * @revision      $Id:$
 * @created       Apr 22, 2011
 * @package       Images
 * @subpackage	  Basic
 * @category      Utillites
 * @version       1.0.0
 * @desc          Basic manipulation with image and uttilites class
 * @copyright     Copyright Alexey Gordeyev IK (c) 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 */

Class Images_Basic
{
   /**
    * 
    * Convert color from hex to rgb
    * @access public
    * @param  string $color
    * @return array $rgb
    */
   public static function hex2rgb($color) {
      if (is_array($color)) return $color;
      $color = str_replace('#', '', $color);
      $s = strlen($color) / 3;
      $rgb[] = hexdec(str_repeat(substr($color, 0, $s), 2 / $s));
      $rgb[] = hexdec(str_repeat(substr($color, $s, $s), 2 / $s));
      $rgb[] = hexdec(str_repeat(substr($color, 2 * $s, $s), 2 / $s));
      return $rgb;
   }
}


?>