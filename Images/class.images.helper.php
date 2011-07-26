<?php
/**
 * @revision      $Id$
 * @created       Jul 22, 2011
 * @package       Images
 * @subpackage	  Helper
 * @category      Utillites
 * @version       1.0.0
 * @desc          Basic manipulation with image
 * @copyright     Copyright Alexey Gordeyev IK © 2009-2011 - All rights reserved.
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
      if(file_exists($image_source)) {
         $image = self::createimagefromsource($image_source);
         imagefilter($image,IMG_FILTER_GRAYSCALE);
      }
      return $image;
   }

   /**
    * Conver image to negative
    * @access public
    * @param  string $image_source
    * @return string $image
    */
   public static function imagetonegative($image_source)
   {
      $image = false;
      if(file_exists($image_source)) {
         $image = self::createimagefromsource($image_source);
         imagefilter($image,IMG_FILTER_NEGATE);
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
         }
      }
      return $image;
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
      header('content-type: '.$type);
      header('Content-Length: ' . strlen($image));
      $type = str_replace('mime/','',$type);

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
    * @return string
    */
   public static function saveimage($image,$destination,$prefix='new_',$type='png',$quality=100)
   {
      $type = strtolower($type);
      # build new image name
      if($prefix || $type) {
         $destination = self::buildimagename($destination,$prefix,$type);
      }

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
            // ,$quality echo $quality;
            imagepng($image,$destination);
            break;
         default:
            echo 'Unsupported image file format.';
      }
      // imagedestroy($image);
      return $destination;
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
    * Get image width
    * @access public
    * @param  string $image_source
    * @return int
    */
   public static function width($image_source)
   {
      return self::getimageinfo($image_source,'width');
   }

   /**
    * Get image height
    * @access public
    * @param  string $image_source
    * @return int
    */
   public static function height($image_source)
   {
      return self::getimageinfo($image_source,'height');
   }

   /**
    * Get image mime/type
    * @access public
    * @param  string $image_source
    * @return int
    */
   public static function mime($image_source)
   {
      return self::getimageinfo($image_source,'mime');
   }
    
   /**
    * Clear image filename
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
   public static function buildimagename($old_filename,$prefix,$type)
   {
      $path_parts = pathinfo($old_filename);
      $slash = '/';
      $new_filename  = $path_parts['dirname'].$slash;
      $new_filename .= $prefix.$path_parts['filename'];
      $new_filename .= '.'.$type;
      return $new_filename;
   }

   /**
    * Proportional resize image
    * @access public
    * @param  string $image_source
    * @param  int    $ratio
    * @return string
    */
   public static function resizeimage($image_source,$new_size,$dimension = "width")
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $image_source = self::createimagefromsource($image_source);
      if($dimension == 'width') {
         $ratio = $width_src/$new_size;
      } else {
         $ratio = $height_src/$new_size;
      }
      $width_dest  = round($width_src/$ratio);
      $height_dest = round($height_src/$ratio);

      $image_destination = imagecreatetruecolor($width_dest,$height_dest);
      imagecopyresized($image_destination, $image_source, 0, 0, 0, 0, $width_dest, $height_dest, $width_src, $height_src);
      return $image_destination;
   }

   /**
    * Resize image to fixed dimensions
    * @access public
    * @param  string $image_source
    * @param  int    $width
    * @param  int    $height
    * @return string $new_image
    */
   public static function resize($image_source,$width,$height)
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $image_source = self::createimagefromsource($image_source);
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $image_source, 0, 0, 0, 0, $width, $height, $width_src, $height_src);
      return $new_image;
   }

   /**
    * Scale image size
    * @access public
    * @param  string $image_source
    * @param  int    $ratio
    * @return string
    */
   public static function scale($image_source,$ratio)
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $width_dest   = round($width_src * $ratio/100);
      $height_dest  = round($height_src * $ratio/100);      
      return self::resize($image_source,$width_dest,$height_dest);
   }
    
   /**
    * Build image reflection
    * @access public
    * @param  string $image_source
    * @param  double $reflection_height
    * @return string $buffer
    */
   public static function buildreflection($image_source,$ratio=30)
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $image_source = self::createimagefromsource($image_source);

      # calculate reflection height
      $reflection_height = round($height_src * ($ratio/100));

      #	We'll store the final reflection in $output. $buffer is for internal use.
      $output = imagecreatetruecolor($width_src, $reflection_height);
      $buffer = imagecreatetruecolor($width_src, $reflection_height);

      #	Copy the bottom-most part of the source image into the output
      imagecopy($output, $image_source, 0, 0, 0, $height_src - $reflection_height,$width_src, $reflection_height);

      #	Rotate and flip it (strip flip method)
      for ($y = 0; $y < $reflection_height; $y++)
      {
         imagecopy($buffer, $output, 0, $y, 0, $reflection_height - $y - 1, $width_src, 1);
      }
      imagedestroy($output);
      return $buffer;
   }

   /**
    * Build Image with Reflection
    * @access public
    * @return void
    */
   public static function buildimagewithreflection($image_source,$reflection_source=false,$ratio=30)
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
       
      if(!$reflection_source) {
         $reflection_source = self::buildreflection($image_source,$ratio);
      }
      $image_source = self::createimagefromsource($image_source);

      # calculate reflection height
      $reflection_height = round($height_src * ($ratio/100));

      $finaloutput = imagecreatetruecolor($width_src, $height_src + $reflection_height);
      imagecopy($finaloutput,$image_source, 0, 0, 0, 0, $width_src, $height_src);
      imagecopy($finaloutput,$reflection_source, 0, $width_src, 0, 0, $width_src, $reflection_height);

      return $finaloutput;
   }

}


?>