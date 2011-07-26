<?php
/**
 * @revision      $Id$
 * @created       Apr 22, 2011
 * @package       Images
 * @subpackage	  Reflection
 * @category      Utillites
 * @version       1.0.0a
 * @copyright     Copyright Alexey Gordeyev IK © 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 */

Class Images_Reflection {

   var $path_to_source          = null;    // The full input image path
   var $path_to_target          = 'img/';  // Path to the output image dir
   var $path_to_temp            = 'tmp/';  // The temporary image path
   var $image_name              = null;    // The image name
   var $target_image_prefix     = 'ref_';  // The prefix of the image with reflection
   var $image_source            = null;    // The input image source
   var $reflection_source       = null;    // The image reflection source
   var $path_info               = array(); // Path information
   var $image_info              = array(); // Image information
   var $reflection_height       = 50;      // Reflection height
   var $reflection_transparency = 30;      // Reflection transparency
   var $hex_background          = '#FFF';  // Background color
   var $divider                 = 1;       // Size of the divider line
   var $alpha_start             = 75;      // 
   var $alpha_end               = 0;       // 
   var $width                   = 0;       // Image width
   var $height                  = 0;       // Image height
   var $type                    = null;    // The image type
   var $mime                    = null;    // Image mime/type
   var $curl_timeout            = 5;       // curl timeout

   /**
    *
    * Class constructor
    * @access public
    * @return void
    */
   function __construct($path_to_source = false,$path_to_target = false,$path_to_temp = false)
   {
      $this->setPathToTarget($path_to_target);
      $this->setPathToTemp($path_to_temp);
      if($path_to_source) {
         if(mb_substr($path_to_source, 0, 7) == 'http://') {
            $this->downloadImage($path_to_source);
         }
         $this->loadImageSource();
      }
   }

   /**
    *
    * Set backgroud color
    * @access public
    * @param  string $hex_background
    * @return void
    */
   public function setBackgoundColor($hex_background)
   {
      $this->hex_background = $hex_background;
   }

   /**
    * Set full path to the source image
    * @access public
    * @param  string $path_to_source
    * @return void
    */
   public function setPathToSource($path_to_source)
   {
      # get path information
      $this->path_info = pathinfo($path_to_source);
      $this->image_name = $this->path_info['basename'];
      $this->path_to_source = $path_to_source;
   }

   /**
    *
    * Set path to the Target directory
    * @access public
    * @param  string $path_to_target
    * @return void
    */
   public function setPathToTarget($path_to_target = false)
   {
      # set path
      if($path_to_target) { $this->path_to_target = $path_to_target; }
      # create folder if needed
      $this->checkPath($this->path_to_target);
   }

   /**
    *
    * Set and create path to temporary directory
    * @access public
    * @param  string $path_to_temp
    * @return void
    */
   public function setPathToTemp($path_to_temp = false)
   {
      # set path
      if($path_to_temp) { $this->path_to_temp = $path_to_temp; }
      # create folder if needed
      $this->checkPath($this->path_to_temp);
   }

   /**
    *
    * Check if directory exsist
    * @access public
    * @param  string $path
    * @return void
    */
   public function checkPath($path)
   {
      if (!file_exists($path)) {
         if (!mkdir($path, 0, true)) {
            echo $path;
            die('Failed to create folder '.$path);
         }
      }
   }

   /**
    *
    * Get Image Information
    * @access public
    * @return void
    */
   public function setImageInfo($path_to_source = false)
   {
      $source = ($path_to_source)?$path_to_source:$this->path_to_source;
      # read information from image file
      $this->image_info   = getimagesize($source);
      # set image dimensions
      $this->width  = $this->image_info[0];
      $this->height = $this->image_info[1];
      $this->type   = $this->image_info[2];
      # get image mime type
      $this->mime   = $this->image_info['mime'];
   }

   /**
    * Get Image Information
    * @access public
    * @return array $image_info
    */
   public function getImageInfo()
   {
      return $this->image_info;
   }

   /**
    * Download Image from remote server
    * @access public
    * @param  string $path_to_source
    * @param  string $path_to_target
    * @return bool
    */
   public function downloadImage($path_to_source = false,$path_to_temp = false)
   {
      if(!$path_to_source && !$this->path_to_source) {
         return false;
      }
      else {
         # set path to image source
         $this->setPathToSource($path_to_source);
         # set path to temporary folder
         $this->setPathToTemp($path_to_temp);
         # cURL initialization
         $ch = curl_init();
         curl_setopt($ch,CURLOPT_URL,$path_to_source);
         curl_setopt($ch,CURLOPT_FAILONERROR, 1);
         curl_setopt($ch,CURLOPT_FOLLOWLOCATION, 1);
         curl_setopt($ch,CURLOPT_AUTOREFERER, 1);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
         # set cURL connection timeout
         curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$this->curl_timeout);
         # execute cURL
         $this->image_source = curl_exec($ch);
         # write image to temporary folder
         file_put_contents($this->path_to_temp.$this->image_name,$this->image_source);
         # set new path to source
         $this->setPathToSource($this->path_to_temp.$this->image_name);
         # read information from image source
         $this->setImageInfo();
         # close cURL
         curl_close($ch);
         return true;
      }
   }

   /**
    * Load Image Source
    * @access public
    * @return void
    */
   public function loadImageSource()
   {
      switch ($this->type)
      {
         case 1:
            //	GIF
            $this->image_source = imagecreatefromgif($this->path_to_source);
            break;
             
         case 2:
            //	JPG
            $this->image_source = imagecreatefromjpeg($this->path_to_source);
            break;
             
         case 3:
            //	PNG
            $this->image_source = imagecreatefrompng($this->path_to_source);
            break;
             
         default:
            echo 'Unsupported image file format.';
            exit();
      }
   }

   /**
    * Save Image To target directory
    * @access public
    * @param  string $path_to_temp
    * @return void
    */
   public function saveImage($path_to_target = false)
   {
      if(!$path_to_target && !$this->path_to_target) {
         return;
      }
      else {
         $this->setPathToTarget($path_to_target);
         $this->checkPath($this->path_to_target);
      }

      if($this->image_source && $this->image_name) {
         imagejpeg($this->image_source, $this->path_to_target.$this->image_name, 100);
      }
      else {
         return;
      }
   }

   /**
    * Save Reflection To target directory
    * @access public
    * @param  string $path_to_temp
    * @return void
    */
   public function saveReflection($path_to_target = false)
   {
      if(!$path_to_target && !$this->path_to_target) {
         return;
      }
      else {
         $this->setPathToTarget($path_to_target);
         $this->checkPath($this->path_to_target);
      }

      if($this->reflection_source && $this->image_name) {
         imagejpeg($this->image_source, $this->path_to_target.$this->target_image_prefix.$this->image_name, 100);
      }
      else {
         return;
      }
   }

   /**
    * Show Reflection Image
    * @access public
    * @return return_type bare_field_name
    */
   public function showReflection()
   {
      header('content-type: image/png');
      imagepng($this->image_source);
      imagedestroy($this->image_source);
   }

   /**
    * Enter description here ... showImage
    * @access public
    * @return return_type bare_field_name
    */
   public function showImage($only_reflection)
   {
      $source = ($only_reflection)?$this->reflection_source:$this->image_source;
      header('content-type: '.$this->mime);
      switch ($this->type)
      {
         case 1:
            //	GIF
            imagegif($source);
            break;
             
         case 2:
            //	JPG
            imagejpeg($source, '', 100);
            break;
             
         case 3:
            //	PNG
            imagepng($source);
            break;
             
         default:
            echo 'Unsupported image file format.';
            exit();
      }
      imagedestroy($source);
   }

   /**
    * Build Reflection
    * @access public
    * @return void
    */
   public function buildReflection()
   {
      #	We'll store the final reflection in $output. $buffer is for internal use.
      $output = imagecreatetruecolor($this->width, $this->reflection_height);
      $buffer = imagecreatetruecolor($this->width, $this->reflection_height);

      #	Copy the bottom-most part of the source image into the output
      imagecopy($output, $this->image_source, 0, 0, 0, $this->height - $this->reflection_height, $this->width, $this->reflection_height);

      #	Rotate and flip it (strip flip method)
      for ($y = 0; $y < $this->reflection_height; $y++)
      {
         imagecopy($buffer, $output, 0, $y, 0, $this->reflection_height - $y - 1, $this->width, 1);
      }

      $this->reflection_source = $buffer;
   }


   /**
    * Build Fade
    * @access public
    * @return return_type bare_field_name
    */
   public function buildFade()
   {

      #	Does it start with a hash? If so then strip it
      $hex_background = str_replace('#', '', $this->hex_background);

      switch (strlen($hex_background))
      {
         case 6:
            $red = hexdec(substr($hex_background, 0, 2));
            $green = hexdec(substr($hex_background, 2, 2));
            $blue = hexdec(substr($hex_background, 4, 2));
            break;
         case 3:
            $red = substr($hex_background, 0, 1);
            $green = substr($hex_background, 1, 1);
            $blue = substr($hex_background, 2, 1);
            $red = hexdec($red . $red);
            $green = hexdec($green . $green);
            $blue = hexdec($blue . $blue);
            break;
         default:
            //	Wrong values passed, default to black
            $red = 127;
            $green = 127;
            $blue = 127;
      }

      $alpha_length = abs($this->alpha_start - $this->alpha_end);
      imagelayereffect($this->reflection_source, IMG_EFFECT_OVERLAY);

      for ($y = 0; $y <= $this->reflection_height; $y++)
      {
         //  Get % of reflection height
         $pct = $y / $this->reflection_height;
         //  Get % of alpha
         if ($this->alpha_start > $this->alpha_end)
         {
            $alpha = (int) ($this->alpha_start - ($pct * $alpha_length));
         }
         else
         {
            $alpha = (int) ($this->alpha_start + ($pct * $alpha_length));
         }
         //  Rejig it because of the way in which the image effect overlay works
         $final_alpha = 127 - $alpha;
         //imagefilledrectangle($output, 0, $y, $width, $y, imagecolorallocatealpha($output,  $red, $green, $blue, $final_alpha));
         file_put_contents('colors.txt',$red.' '.$green.' '.$blue.' '.$final_alpha);
         $fade = imagecolorallocatealpha($this->reflection_source, $red, $green, $blue, $final_alpha);
         imagefilledrectangle($this->reflection_source, 0, $y, $this->width, $y, $fade);
      }
   }

   /**
    * Build Image with Reflection
    * @access public
    * @return void
    */
   public function buildImageWithReflection()
   {
      if(!$this->reflection_source) { $this->buildReflection(); }
      $finaloutput = imagecreatetruecolor($this->width, $this->height+$this->reflection_height);
      imagecopy($finaloutput, $this->image_source, 0, 0, 0, 0, $this->width, $this->height);
      imagecopy($finaloutput, $this->reflection_source, 0, $this->height, 0, 0, $this->width, $this->reflection_height);
      $this->image_source = $finaloutput;
   }
   
}

?>