<?php
/**
 * @revision      $Id$
 * @created       Jul 22, 2011
 * @package       Images
 * @subpackage	  Helper
 * @category      Utillites
 * @version       1.0.0
 * @desc          Basic manipulation with image and uttilites class
 * @copyright     Copyright Alexey Gordeyev IK (c) 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 */

Class ImagesHelper
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
      header('Content-Length: ' . strlen($image));
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
    * Save image in to file
    * @access public
    * @param  string $image
    * @param  string $destination
    * @param  string $prefix
    * @param  string $type
    * @param  int    $quality
    * @return void
    */
   public static function saveimage($image,$destination,$prefix='new_',$type='png',$quality=100)
   {
      # build new image name
      if($prefix) {
         $destination = self::buildimagename($destination,$prefix);
      }
      $type = strtolower($type);

      switch ($type)
      {
         case 'gif':
            imagegif($image,$destination);
            break;
         case 'jpg':
         case 'jpeg':
            imagejpeg($image, $destination,$quality);
            break;
         case 'png':
            imagepng($image,$destination,$quality);
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

   /**
    * Clear image name
    * @access public
    * @param  string $filename
    * @return string $imagename
    */
   public static function clearimagename($filename)
   {
      $imagename = trim($filename);
      $imagename = strtolower($string);
      $imagename = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $imagename));
      $imagename = ereg_replace("[ \t\n\r]+", "_", $imagename);
      $imagename = str_replace(" ", '_', $imagename);
      $imagename = ereg_replace("[ _]+", "_", $imagename);

      return $imagename;
   }

   /**
    * Build new image filename name with prefix
    * @access public
    * @param  string $old_filename
    * @param  string $prefix
    * @return string $new_filename
    */
   public static function buildimagename($old_filename,$prefix)
   {
      $path_parts = pathinfo($old_filename);
      $slash = strstr(PHP_OS,'WIN') ? '\/' : '/';
      $new_filename  = $path_parts['dirname'].$slash;
      $new_filename .= $prefix.$path_parts['basename'];
      $new_filename .= '.'.$path_parts['extension'];
      return $new_filename;
   }
    
}


?>