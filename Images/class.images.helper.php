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
    * Convert color from hex to rgb
    * @access public
    * @param  string $color - color in hex format
    * @return array         - rgb color as array
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
    * @param  string $image_source - path to image source
    * @return mixed                - resulted image object or false
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
    * @param  string $image_source - path to image source
    * @return mixed                - resulted image object or false
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
    * @param  string $image_source - path to image source
    * @return mixed                - image object or false
    */
   public static function createimagefromsource($image_source)
   {
      $image = false;
      if(file_exists($image_source)) {
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
      }
      return $image;
   }

   /**
    * Output image in to browser
    * @access public
    * @param  object $image   - source image as object 
    * @param  string $type    - output image format (default = png)
    * @param  int    $quality - resulted image quality in % (default = 100)
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
    * @param  object $image       - image source as object
    * @param  string $destination - output destination path and filename
    * @param  string $prefix      - prefix for new image filename (default = new_)
    * @param  string $type        - new image format (default = png)
    * @param  int    $quality     - resulted image quality (default = 100)
    * @return string              - full path to resulted image 
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
    * @param  string $image_source - pat to image source
    * @param  string $info_type    - type of the returned information
    * @return mixed                - source image information (int, string or array)
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
    * @param  string $image_source - path to image source
    * @return int                  - source image width in px
    */
   public static function width($image_source)
   {
      return self::getimageinfo($image_source,'width');
   }

   /**
    * Get image height
    * @access public
    * @param  string $image_source - path to image source
    * @return int                  - source image width in px
    */
   public static function height($image_source)
   {
      return self::getimageinfo($image_source,'height');
   }

   /**
    * Get image mime/type
    * @access public
    * @param  string $image_source - path to image source
    * @return string               - source image mime/type
    */
   public static function mime($image_source)
   {
      return self::getimageinfo($image_source,'mime');
   }

   /**
    * Clear (normalize) image filename
    * @access public
    * @param  string $filename  - source image filename
    * @return string $imagename - cleared filename
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
    * @param  string $old_filename - source image filename
    * @param  string $prefix       - filename prefix
    * @return string               - new image filename
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
    * @param  string $image_source - path to image source
    * @param  int    $ratio        -
    * @param  string $dimension    -
    * @return object               - the resulting image
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

      $destination_image = imagecreatetruecolor($width_dest,$height_dest);
      imagecopyresized($destination_image, $image_source, 0, 0, 0, 0, $width_dest, $height_dest, $width_src, $height_src);
      return $destination_image;
   }

   /**
    * Resize image to fixed dimensions
    * @access public
    * @param  string $image_source - path to image source
    * @param  int    $width        - new image width in px
    * @param  int    $height       - new image height in px
    * @return object               - the resulting image
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
    * @param  string $image_source - path to image source
    * @param  int    $ratio        - image scaling value in %
    * @return object               - the resulting image
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
    * Build quadrate image
    * @access public
    * @param  string $image_source - path to image source
    * @param  int    $ratio        - resize ratio
    * @param  string $dimension    - ration dimension % or px
    * @return object               - the resulting image
    */
   public static function quadrate($image_source,$ratio,$dimension='px')
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $image_source = self::createimagefromsource($image_source);
      if($dimension == 'px') {
         $width_dest   = $ratio;
      } else {
         $width_dest   = round($width_src * $ratio/100);
      }

      $destination_image = imagecreatetruecolor($width_dest,$width_dest);

      if ($width_src>$height_src) {
         imagecopyresized($destination_image,$image_source, 0, 0,
         round((max($width_src,$height_src)-min($width_src,$height_src))/2),
         0, $width_dest, $width_dest, min($width_src,$height_src), min($width_src,$height_src));
      }

      if ($width_src<$height_src) {
         imagecopyresized($destination_image,$image_source, 0, 0, 0, 0, $width_dest, $width_dest,
         min($width_src,$height_src), min($width_src,$height_src));
      }

      if ($width_src==$height_src) {
         imagecopyresized($destination_image,$image_source, 0, 0, 0, 0, $width_dest, $width_dest, $width_src, $width_src);
      }

      return $destination_image;
   }

   /**
    * Build image reflection
    * @access public
    * @param  string $image_source      - path to image source
    * @param  int    $ratio             - reflection ratio %
    * @return object                    - the resulting image
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
    * @param  string $image_source      - path to image source
    * @param  mixed  $reflection_source - path to reflection image source (default = false)
    * @param  int    $ratio             - reflection height in % of the main image
    * @return object                    - the resulting image
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

   /**
    * Rotate image
    * @access public
    * @param  string $image_source - path to image source
    * @param  int    $degrees      - rotate angle
    * @param  string $bg_color     - background color (default empty)
    * @param  bool   $transparent  - transparent background (default = true) 
    * @return object               - the resulting image
    */
   public static function rotate($image_source,$degrees,$bg_color='',$transparent=true)
   {
      $rgb_color = false;
      if($bg_color) {
         if(is_array($bg_color)) {
            $rgb_color = $bg_color;
         } elseif($bg_color != '') {
            $rgb_color = self::hextorgb($bg_color);
         }
      }

      $image_source = self::createimagefromsource($image_source);
      $allocated_bg_color = false;
      if(is_array($rgb_color)) {
         $allocated_bg_color = imagecolorallocate($image_source, $rgb_color[0], $rgb_color[1], $rgb_color[2]);
      }
      $final_bg_color = ($allocated_bg_color)?$allocated_bg_color:0;
      $rotated_image = imagerotate($image_source, $degrees, $final_bg_color, 0);
      // $rgb_color
      if($transparent) {
         imagecolortransparent($rotated_image, $final_bg_color);
      }

      return $rotated_image;
   }

   /**
    * Build watermark text
    * @access public
    * @param  string $image_source - path to image source
    * @param  string $text         - watemark text
    * @param  string $font         - path to watemark font file 
    * @param  string $color        - watemark text color in hex format (default white #fff)
    * @param  imt    $alpha_level  - watemark text alpha level (default = 100)
    * @return object               - the resulting image
    */
   function buildwatermarktext( $image_source, $text, $font, $color = '#fff', $alpha_level = 100 )
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $angle        =  -rad2deg(atan2((-$height_src),($width_src)));
      $rgb_color    = self::hextorgb($color);

      $image_source = self::createimagefromsource($image_source);
      $text = " ".$text." ";

      $c = imagecolorallocatealpha($image_source, $rgb_color[0], $rgb_color[1], $rgb_color[2], $alpha_level);
      $size = (($width_src+$height_src)/2)*2/strlen($text);
      $box  = imagettfbbox( $size, $angle, $font, $text );
      $x = $width_src/2 - abs($box[4] - $box[0])/2;
      $y = $height_src/2 + abs($box[5] - $box[1])/2;

      imagettftext($image_source,$size ,$angle, $x, $y, $c, $font, $text);
      return $image_source;
   }

   /**
    * Build watermarked image from another image
    * @access public
    * @param  string $image_source  - path to image source
    * @param  string $watermark_img - path to watemark image source
    * @param  int    $alpha_level   - watemark image aplha level (default = 100)
    * @param  int    $x             - watemark coordinates on the x-axis (default = 5)
    * @param  int    $y             - watemark coordinates on the y-axis (default = 5)
    * @return object                - the resulting image
    */
   function buildwatermarkfromimage($image_source, $watermark_img, $alpha_level = 100, $x = 5, $y = 5)
   {
      $watermark_width  = self::width($watermark_img);
      $watermark_height = self::height($watermark_img);
      $watermark_img = self::createimagefromsource($watermark_img);
      
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $image_source = self::createimagefromsource($image_source);
      
      $dest_x = $width_src - $watermark_width - $x;
      $dest_y = $height_src - $watermark_height - $y;
      imagecopymerge($image_source, $watermark_img, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $alpha_level);

      return $image_source;
   }
    
}


?>