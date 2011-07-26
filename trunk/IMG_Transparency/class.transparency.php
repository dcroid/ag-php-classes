<?php
/**
 * @revision      $Id:$
 * @created       Apr 22, 2011
 * @category      Images
 * @package       Transparency
 * @version       1.0.0
 * @desc          Image Transparency Class
 * @copyright     Copyright Alexey Gordeyev IK � 2009-2011 - All rights reserved.
 * @license       GPLv2
 * @author        Alexey Gordeyev IK <aleksej@gordejev.lv>
 * @link          http://www.agjoomla.com/classes/
 */


class IMG_Transparency {

   var $source_image;
   var $pct;
   var $new_image_name;
   var $save_to_folder;

   function buildtransparent()
   {
      $info = GetImageSize($this->source_image);
      $width = $info[0];
      $height = $info[1];

      $mime = $info['mime'];

      // What sort of image?

      $type = substr(strrchr($mime, '/'), 1);

      switch ($type)
      {
         case 'jpeg':
            $image_create_func = 'ImageCreateFromJPEG';
            $image_save_func = 'ImageJPEG';
            $new_image_ext = 'jpg';
            break;

         case 'png':
            $image_create_func = 'ImageCreateFromPNG';
            $image_save_func = 'ImagePNG';
            $new_image_ext = 'png';
            break;

         case 'bmp':
            $image_create_func = 'ImageCreateFromBMP';
            $image_save_func = 'ImageBMP';
            $new_image_ext = 'bmp';
            break;

         case 'gif':
            $image_create_func = 'ImageCreateFromGIF';
            $image_save_func = 'ImageGIF';
            $new_image_ext = 'gif';
            break;

         case 'vnd.wap.wbmp':
            $image_create_func = 'ImageCreateFromWBMP';
            $image_save_func = 'ImageWBMP';
            $new_image_ext = 'bmp';
            break;

         case 'xbm':
            $image_create_func = 'ImageCreateFromXBM';
            $image_save_func = 'ImageXBM';
            $new_image_ext = 'xbm';
            break;

         default:
            $image_create_func = 'ImageCreateFromJPEG';
            $image_save_func = 'ImageJPEG';
            $new_image_ext = 'jpg';
      }

      // Source Image
      $image = $image_create_func($this->source_image);

      $new_image = ImageCreateTruecolor($width, $height);

      // Set a White & Transparent Background Color
      $bg = ImageColorAllocateAlpha($new_image, 255, 255, 255, 127); // (PHP 4 >= 4.3.2, PHP 5)
      ImageFill($new_image, 0, 0 , $bg);

      // Copy and merge
      ImageCopyMerge($new_image, $image, 0, 0, 0, 0, $width, $height, $this->pct);

      if($this->save_to_folder)
      {
         if($this->new_image_name)
         {
            $new_name = $this->new_image_name.'.'.$new_image_ext;
         }
         else
         {
            $new_name = $this->new_image_name(basename($this->source_image)).'_transparent'.'.'.$new_image_ext;
         }

         $save_path = $this->save_to_folder.$new_name;
      }
      else
      {
         /* Show the image without saving it to a folder */
         header("Content-Type: ".$mime);

         $image_save_func($new_image);

         $save_path = '';
      }

      // Save image

      $process = $image_save_func($new_image, $save_path) or die("There was a problem in saving the new file.");

      return array('result' => $process, 'new_file_path' => $save_path);
   }

   function setnewimagename($filename)
   {
      $string = trim($filename);
      $string = strtolower($string);
      $string = trim(ereg_replace("[^ A-Za-z0-9_]", " ", $string));
      $string = ereg_replace("[ \t\n\r]+", "_", $string);
      $string = str_replace(" ", '_', $string);
      $string = ereg_replace("[ _]+", "_", $string);

      return $string;
   }

}

?>