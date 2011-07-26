<?php
/**
 * @revision      $Id$
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
    * @return array  $rgb
    */
   public static function hextorgb($color) {
      if (is_array($color)) return $color;
      $color = str_replace('#', '', $color);
      $s = strlen($color) / 3;
      $rgb[] = hexdec(str_repeat(substr($color, 0, $s), 2 / $s));
      $rgb[] = hexdec(str_repeat(substr($color, $s, $s), 2 / $s));
      $rgb[] = hexdec(str_repeat(substr($color, 2 * $s, $s), 2 / $s));
      return $rgb;
   }

   /**
    * Convert color image to grayscale
    * @access public
    * @param  string $image_source
    * @param  string $prefix
    * @return mixed
    */
   public static function colortograyscale($image_source)
   {
      $image = false;
      if(!file_exists($image_source)) {
         $image = self::createimagefromsource($image_source);
         imagefilter($image,IMG_FILTER_GRAYSCALE);
      }
      return $image;
   }

   /**
    *
    * Create image from source
    * @access public
    * @param  string $image_source
    * @return mixed
    */
   public static function createimagefromsource($image_source)
   {
      $image = false;
      $image_type = self::getimageinfo($image_source,'type');

      if($image_type) {
         switch ($image_type)
         {
            case 1:
               # GIF
               $image = imagecreatefromgif($image_source);
               break;
            case 2:
               # JPG
               $image = imagecreatefromjpeg($image_source);
               break;
            case 3:
               # PNG
               $image = imagecreatefrompng($image_source);
               break;
            default:
               # Uncnown format
               $image = false;
         }
      }
      return $image;
   }

   /**
    *
    * Save image to local file
    * @access public
    * @param  string $image
    * @param  string $destination
    * @param  string $type
    * @return mixed
    */
   public static function saveimagetofile($image,$destination,$type=false)
   {

   }

   /**
    * Show image to browser
    * @access public
    * @param  string $image
    * @param  string $type
    * @param  int    $quality
    * @return void
    */
   public static function showimage($image,$type='png',$quality=100)
   {
      $type = strtolower($type);
      header('content-type: image/'.$type);
      switch ($type)
      {
         case 'gif':
            imagegif($image);
            break;
         case 'jpg':
         case 'jpeg':
            imagejpeg($image, '',$quality);
            break;
         case 'png':
            imagepng($image);
            break;
         default:
            echo 'Unsupported image file format.';
      }
      imagedestroy($image);
   }
    
   /**
    * Get basic information from Image source
    * @access public
    * @param  string $image_source
    * @param  string $info_type
    * @return mixed
    */
   public static function getimageinfo($image_source,$info_type=false)
   {
      # read information from image file
      $image_info   = getimagesize($image_source);
      if($image_info) {
         if($info_type) {
            switch($info_type) {
               case 'width':
                  return $image_info[0];
                  break;
               case 'height':
                  return $image_info[1];
                  break;
               case 'type':
                  return $image_info[2];
                  break;
               case 'mime':
                  return $image_info['mime'];
                  break;
               default;
               return $image_info;
            }
         } else { return $image_info; }
      } else { return false; }
   }



}


?>