<?php
/**
 * @revision      $Id$
 * @created       Jul 22, 2011
 * @package       Images
 * @subpackage	  Helpers
 * @category      Auxiliary
 * @version       1.0.0
 * @desc          Basic manipulation with image
 * @copyright     Copyright Alexey Gordeyev IK © 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 * @source        http://code.google.com/p/ag-php-classes/
 */

Class ImagesHelper
{
   /**
    * Convert color from hex to rgb
    * @access public
    * @param  string $color - color in hex format
    * @return array         - rgb color as array
    */
   public static function hextorgb($color)
   {
      if (is_array($color)) return $color;
      $color = str_replace('#', '', $color);
      $s = strlen($color) / 3;
      $rgb[] = hexdec(str_repeat(substr($color, 0, $s), 2 / $s));
      $rgb[] = hexdec(str_repeat(substr($color, $s, $s), 2 / $s));
      $rgb[] = hexdec(str_repeat(substr($color, 2 * $s, $s), 2 / $s));
      return $rgb;
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
      if(self::isgdresource($image_source)) {
         $image = $image_source;
      } else {
         if(file_exists($image_source)) {
            $image_type = self::getimageinfo($image_source,'type');

            if($image_type) {
               switch ($image_type)
               {
                  case 1:
                  case IMAGETYPE_GIF:
                     $image = imagecreatefromgif($image_source);
                     break;
                  case 2:
                  case IMAGETYPE_JPEG:
                     $image = imagecreatefromjpeg($image_source);
                     break;
                  case 3:
                  case IMAGETYPE_PNG:
                     $image = imagecreatefrompng($image_source);
                     break;
                  case 15:
                  case IMAGETYPE_WBMP:
                     $image = imagecreatefromwbmp($image_source);
                     break;
                  case 16:
                  case IMAGETYPE_XBM:
                     $image = imagecreatefromxbm($image_source);
                     break;
               }
            }
         }
      }
      return $image;
   }

   /**
    *
    * Validate gd resource
    * @access public
    * @param  mixed $image_source
    * @return bool
    */
   public static function isgdresource($image_source)
   {
      $gd_resource = false;
      if(gettype($image_source) == 'resource') {
         if(get_resource_type($image_source) == 'gd') {
            $gd_resource = true;
         }
      }
      return $gd_resource;
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
      if(file_exists($image_source) || self::isgdresource($image_source)) {
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
      if(file_exists($image_source) || self::isgdresource($image_source)) {
         $image = self::createimagefromsource($image_source);
         imagefilter($image,IMG_FILTER_NEGATE);
      }
      return $image;
   }

   /**
    * Convert image to another format
    * @access public
    * @param  string $image_source - path to image source
    * @param  string $format       - new image format
    * @param  string $prefix       -
    * @param  string $new_filename - new image filename
    * @return string               - full path to destination
    */
   public static function convert($image_source,$format = 'png',$prefix = '',$new_filename = false)
   {
      $new_filename = $image_source;
      $image = self::createimagefromsource($image_source);
      if($new_filename) {
         $destination = $new_filename;
      }
      return self::saveimage($image,$destination,$prefix,$format);
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
      $type = ($type == 'jpg')?'jpeg':$type;
      $type = ($type == 'wbmp')?'vnd.wap.wbmp':$type;
      header('Content-type: image/'.$type);
      // header('Content-Length: ' . strlen($image));
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
         case 'vnd.wap.wbmp':
            imagewbmp($image);
            break;
         case 'xbm':
            imagexbm($image);
            break;
         default:
            echo 'Unsupported image file format.';
      }
      imagedestroy($image);
   }

   /**
    * Save image in to file
    * @access public
    * @param  object $image       - image object
    * @param  string $destination - output destination path and filename
    * @param  string $prefix      - prefix for new image filename (default = new_)
    * @param  string $type        - new image format (default = png)
    * @param  int    $quality     - resulted image quality (default = 100)
    * @return string              - full path to resulted image
    */
   public static function saveimage($image,$destination,$prefix='',$type='png',$quality=100)
   {
      $type = strtolower($type);
      # build new image name
      if($prefix || $type) {
         if($type != '' || $prefix != '') {
            $destination = self::buildimagename($destination,$prefix,$type);
         }
      }
      if(self::isgdresource($image)) {
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
               imagepng($image,$destination);
               break;
            case 'wbmp':
               imagewbmp($image,$destination);
               break;
            case 'xbm':
               imagexbm($image,$destination);
               break;
            default:
               echo 'Unsupported image file format.';
         }
      }
      else {
         $destination = false;
      }
      return $destination;
   }

   /**
    * Destroy image resource
    * @access public
    * @param  object $image
    * @return bool
    */
   public static function destroy($image)
   {
      if(self::isgdresource($image)) {
         imagedestroy($image);
         return true;
      }
      return false;
   }
    
   /**
    * Get basic information from Image source
    * @access public
    * @param  mixed  $image_source - path to image source or image object
    * @param  string $info_type    - type of the returned information
    * @return mixed                - source image information (int, string or array)
    */
   public static function getimageinfo($image_source,$info_type=false)
   {
      # read information from image file
      if(file_exists($image_source)) {
         $image_info   = getimagesize($image_source);
      }
      elseif(self::isgdresource($image_source)) {
         $image_info    = array();
         $image_info[0] = imagesx($image_source);
         $image_info[1] = imagesy($image_source);
         if (imagetypes() & IMG_PNG) {
            $image_info[2] = 3;
            $image_info['mime'] = 'image/png';
         }
         elseif (imagetypes() & IMG_GIF) {
            $image_info[2] = 1;
            $image_info['mime'] = 'image/gif';
         }
         else {
            $image_info[2] = 2;
            $image_info['mime'] = 'image/jpeg';
         }
      }
      else { $image_info = array(0=>0,1=>0,2=>false,'mime'=>false); }

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
    * @param  mixed  $image_source - path to image source or image object
    * @return int                  - source image width in px
    */
   public static function width($image_source)
   {
      return self::getimageinfo($image_source,'width');
   }

   /**
    * Get image height
    * @access public
    * @param  mixed  $image_source - path to image source or image object
    * @return int                  - source image width in px
    */
   public static function height($image_source)
   {
      return self::getimageinfo($image_source,'height');
   }

   /**
    * Get image mime/type
    * @access public
    * @param  mixed  $image_source - path to image source or image object
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
   public static function buildimagename($old_filename,$prefix,$type=false)
   {
      $path_parts = pathinfo($old_filename);
      $ext = ($type && $type != '')?$type:$path_parts['extension'];
      $slash = '/';
      $new_filename  = $path_parts['dirname'].$slash;
      $new_filename .= $prefix.$path_parts['filename'];
      $new_filename .= '.'.$ext;
      return $new_filename;
   }

   /**
    * Proportional resize image
    * @access public
    * @param  mixed  $image_source - path to image source or image object
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
    * @param  mixed  $image_source - path to image source or image object
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
    * @param  mixed  $image_source - path to image source or image object
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
    * @param  mixed  $image_source - path to image source or image object
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
    * @param  mixed  $image_source - path to image source or image object
    * @param  int    $ratio        - reflection ratio %
    * @return object               - the resulting image
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
    * Build image with reflection
    * @access public
    * @param  mixed  $image_source - path to image source or image object
    * @param  int    $ratio        - reflection ratio %
    * @return object               - the resulting image
    */
   function buildimagewithreflection($image_source,$ratio = 30)
   {
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $image_source = self::createimagefromsource($image_source);

      # calculate reflection height
      $reflection_height = round($height_src * ($ratio/100));

      $reflected = imagecreatetruecolor($width_src,$height_src+$reflection_height);
      imagealphablending($reflected, false);
      imagesavealpha($reflected, true);

      imagecopy($reflected, $image_source, 0, 0, 0, 0, $width_src, $height_src);

      $alpha_step = 80 / $reflection_height;
      for ($y = 1; $y <= $reflection_height; $y++) {
         for ($x = 0; $x < $width_src; $x++) {
            # copy pixel from x / $src_height - y to x / $src_height + y
            $rgb_color = imagecolorat($image_source, $x, $height_src - $y);

            $alpha = ($rgb_color & 0x7F000000) >> 24;
            $alpha =  max($alpha, 47 + ($y * $alpha_step));
            $rgb_color = imagecolorsforindex($image_source, $rgb_color);
            $rgb_color = imagecolorallocatealpha($reflected, $rgb_color['red'], $rgb_color['green'], $rgb_color['blue'], $alpha);
            imagesetpixel($reflected, $x, $height_src + $y - 1, $rgb_color);
         }
      }

      return $reflected;
   }

   /**
    * Fade image
    * @access public
    * @param  mixed  $image_source - path to image source or image object
    * @param  int    $alpha_start
    * @param  int    $alpha_end
    * @param  string $bg_color
    * @return object
    */
   public static function fade($image_source,$alpha_start,$alpha_end,$bg_color = "#fff")
   {
      if ($alpha_start < 1 || $alpha_start > 127) { $alpha_start = 80; }
   	  if ($alpha_end < 1 || $alpha_end > 0)	{ $alpha_end = 0; }
      $alpha_length = abs($alpha_start - $alpha_end);
      $rgb_color    = self::hextorgb($bg_color);
      $width_src    = self::width($image_source);
      $height_src   = self::height($image_source);
      $faded        = self::createimagefromsource($image_source);
      imagelayereffect($faded, IMG_EFFECT_OVERLAY);

      for ($y = 0; $y <= $height_src; $y++)
      {
         # Get % of image height
         $pct = $y / $height_src;

         # Get % of alpha
         if ($alpha_start > $alpha_end) {
            $alpha = (int) ($alpha_start - ($pct * $alpha_length));
         } else {
            $alpha = (int) ($alpha_start + ($pct * $alpha_length));
         }
         # Rejig it because of the way in which the image effect overlay works
         $final_alpha = 127 - $alpha;
         $allocated_color = imagecolorallocatealpha($faded, $rgb_color[0], $rgb_color[1], $rgb_color[2], $final_alpha);
         imagefilledrectangle($faded, 0, $y, $width_src, $y, $allocated_color);
      }
      return $faded;
   }

   /**
    * Rotate image
    * @access public
    * @param  mixed  $image_source - path to image source or image object
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

      # build transparent background
      if($transparent) {
         imagecolortransparent($rotated_image, $final_bg_color);
      }

      return $rotated_image;
   }

   /**
    * Build watermark text
    * @access public
    * @param  mixed  $image_source - path to image source or image object
    * @param  string $text         - watemark text
    * @param  string $font         - path to watemark font file
    * @param  string $color        - watemark text color in hex format (default white #fff)
    * @param  imt    $alpha_level  - watemark text alpha level (default = 100)
    * @return object               - the resulting image
    */
   public static function buildtextwatermark( $image_source, $text, $font, $color = '#fff', $alpha_level = 100 )
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
    * @param  mixed  $image_source  - path to image source or image object
    * @param  string $watermark_img - path to watemark image source
    * @param  int    $alpha_level   - watemark image aplha level (default = 100)
    * @param  int    $x             - watemark coordinates on the x-axis (default = 5)
    * @param  int    $y             - watemark coordinates on the y-axis (default = 5)
    * @return object                - the resulting image
    */
   public static function buildimagewatermark($image_source, $watermark_img, $position = 'random', $alpha_level = 100, $x = 5, $y = 5)
   {
      $watermark_width  = self::width($watermark_img);
      $watermark_height = self::height($watermark_img);
      $watermark_img    = self::createimagefromsource($watermark_img);

      $width_src        = self::width($image_source);
      $height_src       = self::height($image_source);
      $image_source     = self::createimagefromsource($image_source);

      $dest_x = $width_src - $watermark_width - $x;
      $dest_y = $height_src - $watermark_height - $y;
      imagecopymerge($image_source, $watermark_img, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $alpha_level);

      return $image_source;
   }

   /**
    * Calculate watemark position
    * @access public
    * @param  mixed  $src_image - path to image source or image object
    * @param  mixed  $dst_image - path to image destination or image object
    * @param  string $position
    * @return array
    */
   public static function calculateposition($src_image,$dest_image,$position)
   {
      $coordinates = array('dest_x'=>0,
                           'dest_y'=>0,
                           'dest_w'=>0,
                           'dest_h'=>0,
                           'src_x'=>0,
                           'src_y'=>0,
                           'src_w'=>0,
                           'src_h'=>0);

      if ($position == 'random') {
         $position = rand(1,8);
      }
      switch ($position) {
         case 'top-right':
         case 'right-top':
         case 1:
            //  $coordinates[''] = ;
            //  $coordinates[''] = ;
            imagecopy($dst_image, $src_image, ($dest_x-$src_w), 0, 0, 0, $src_w, $src_h);
            break;
         case 'top-left':
         case 'left-top':
         case 2:
            imagecopy($dst_image, $src_image, 0, 0, 0, 0, $src_w, $src_h);
            break;
         case 'bottom-right':
         case 'right-bottom':
         case 3:
            imagecopy($dst_image, $src_image, ($dest_x-$src_w), ($dst_h-$src_h), 0, 0, $src_w, $src_h);
            break;
         case 'bottom-left':
         case 'left-bottom':
         case 4:
            imagecopy($dst_image, $src_image, 0 , ($dst_h-$src_h), 0, 0, $src_w, $src_h);
            break;
         case 'center':
         case 5:
            imagecopy($dst_image, $src_image, (($dest_x/2)-($src_w/2)), (($dst_h/2)-($src_h/2)), 0, 0, $src_w, $src_h);
            break;
         case 'top':
         case 6:
            imagecopy($dst_image, $src_image, (($dest_x/2)-($src_w/2)), 0, 0, 0, $src_w, $src_h);
            break;
         case 'bottom':
         case 7:
            imagecopy($dst_image, $src_image, (($dest_x/2)-($src_w/2)), ($dst_h-$src_h), 0, 0, $src_w, $src_h);
            break;
         case 'left':
         case 8:
            imagecopy($dst_image, $src_image, 0, (($dst_h/2)-($src_h/2)), 0, 0, $src_w, $src_h);
            break;
         case 'right':
         case 9:
            imagecopy($dst_image, $src_image, ($dest_x-$src_w), (($dst_h/2)-($src_h/2)), 0, 0, $src_w, $src_h);
            break;
      }
   }

   /**
    * Draws the gradient
    * @access public
    * @param  string $image_source  - path to image source
    * @param  string $direction     - gradient direction
    * @param  string $start         -
    * @param  string $end           -
    * @return object                - the resulting image
    */
   public static function gradientfill($image_width,$image_height,$direction,$start,$end,$step = 0)
   {
      $r = $g = $b = null;
      list($r1,$g1,$b1) = self::hextorgb($end);
      list($r2,$g2,$b2) = self::hextorgb($start);
      $gradiened        = imagecreatetruecolor($image_width,$image_height);
      $center_x         = $image_width/2;
      $center_y         = $image_height/2;

      switch($direction) {
         case 'horizontal':
         case 'vertical':
            $line_numbers = ($direction == 'vertical')?imagesy($gradiened):imagesx($gradiened);
            $line_width = ($direction == 'vertical')?imagesx($gradiened):imagesy($gradiened);
            list($r1,$g1,$b1) = self::hextorgb($start);
            list($r2,$g2,$b2) = self::hextorgb($end);
            break;
         case 'ellipse':
            $rh=$image_height>$image_width?1:$image_width/$image_height;
            $rw=$image_width>$image_height?1:$image_height/$image_width;
            $line_numbers = min($image_width,$image_height);
            imagefill($gradiened, 0, 0, imagecolorallocate($gradiened, $r1, $g1, $b1 ));
            break;
         case 'ellipse2':
            $rh=$image_height>$image_width?1:$image_width/$image_height;
            $rw=$image_width>$image_height?1:$image_height/$image_width;
            $line_numbers = sqrt(pow($image_width,2)+pow($image_height,2));
            break;
         case 'circle':
            $line_numbers = min($image_width,$image_height);
            $rh = $rw = 1;
            imagefill($gradiened, 0, 0, imagecolorallocate($gradiened, $r1, $g1, $b1));
            break;
         case 'circle2':
            $line_numbers = sqrt(pow($image_width,2)+pow($image_height,2));
            $rh = $rw = 1;
            break;
         case 'square':
         case 'rectangle':
            $line_numbers = max($image_width,$image_height)/2;
            break;
         case 'diamond':
            $rh=$image_height>$image_width?1:$image_width/$image_height;
            $rw=$image_width>$image_height?1:$image_height/$image_width;
            $line_numbers = round(min($image_width,$image_height)*0.95);
            break;
         default:
      }

      for ( $i = 0; $i < $line_numbers; $i=$i+1+$step ) {
         // old values :
         $old_r=$r;
         $old_g=$g;
         $old_b=$b;
         // new values :
         $r = ( $r2 - $r1 != 0 ) ? intval( $r1 + ( $r2 - $r1 ) * ( $i / $line_numbers ) ): $r1;
         $g = ( $g2 - $g1 != 0 ) ? intval( $g1 + ( $g2 - $g1 ) * ( $i / $line_numbers ) ): $g1;
         $b = ( $b2 - $b1 != 0 ) ? intval( $b1 + ( $b2 - $b1 ) * ( $i / $line_numbers ) ): $b1;
         // if new values are really new ones, allocate a new color, otherwise reuse previous color.
         // There's a "feature" in imagecolorallocate that makes this function
         // always returns '-1' after 255 colors have been allocated in an image that was created with
         // imagecreate (everything works fine with imagecreatetruecolor)
         if ( "$old_r,$old_g,$old_b" != "$r,$g,$b") { $fill = imagecolorallocate($gradiened, $r, $g, $b ); }
         switch($direction) {
            case 'vertical':
               imagefilledrectangle($gradiened, 0, $i, $line_width, $i+$step, $fill);
               break;
            case 'horizontal':
               imagefilledrectangle($gradiened, $i, 0, $i+$step, $line_width, $fill );
               break;
            case 'ellipse':
            case 'ellipse2':
            case 'circle':
            case 'circle2':
               imagefilledellipse ($gradiened,$center_x, $center_y, ($line_numbers-$i)*$rh, ($line_numbers-$i)*$rw,$fill);
               break;
            case 'square':
            case 'rectangle':
               imagefilledrectangle ($gradiened,$i*$image_width/$image_height,$i*$image_height/$image_width,$image_width-($i*$image_width/$image_height), $image_height-($i*$image_height/$image_width),$fill);
               break;
            case 'diamond':
               imagefilledpolygon($gradiened, array (
               $image_width/2, $i*$rw-0.5*$image_height,
               $i*$rh-0.5*$image_width, $image_height/2,
               $image_width/2,1.5*$image_height-$i*$rw,
               1.5*$image_width-$i*$rh, $image_height/2 ), 4, $fill);
               break;
            default:
         }
      }
      return $gradiened;
   }

   /**
    *
    * Set tranparent color
    * @access public
    * @param  mixed  $image - 
    * @param  mixed  $color - color hex format
    * @return resource
    */
   public static function tranparent($image_source,$color)
   {
      $rgb_color       = false;
      $image_source    = self::createimagefromsource($watermark_img);
      if($color) {
         if(is_array($color)) {
            $rgb_color = $color;
         } elseif($color != '') {
            $rgb_color = self::hextorgb($color);
         }
      }
      if(is_array($rgb_color)) {
         $allocated_bg_color = imagecolorallocate($image_source, $rgb_color[0], $rgb_color[1], $rgb_color[2]);
      }
      # build transparent background
      if($allocated_bg_color) {
         imagecolortransparent($image_source, $allocated_bg_color);
      }
      return $image_source;
   }

   /**
    * Crop image
    * @access public
    * @param  mixed  $image_source - path to image source or gd image resource
    * @param  int    $src_x        - x-coordinate of source point.
    * @param  int    $src_y        - y-coordinate of source point.
    * @param  int    $crp_width    - destination width
    * @param  int    $crp_height   - destination height
    * @return resource
    */
   public static function crop($image_source, $src_x, $src_y, $crp_width, $crp_height)
   {
      $width_src        = self::width($image_source);
      $height_src       = self::height($image_source);
      $image_source     = self::createimagefromsource($image_source);
      $crp_width        = ($crp_width >= $width_src || $crp_width <= 0)?$width_src:$crp_width;
      $crp_height       = ($crp_height >= $height_src || $crp_height <= 0)?$height_src:$crp_height;
      $destination = imagecreatetruecolor($crp_width, $crp_height);
      imagecopy($destination, $image_source, 0, 0, $src_x, $src_y, $crp_width, $crp_height);
      imagedestroy($image_source);
      return $destination;
   }
}
?>