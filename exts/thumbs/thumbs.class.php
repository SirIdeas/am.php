<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014-2015 Sir Ideas, C. A.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 **/

// Basado en la clase UploadHandler de blueimp
/*
 * jQuery File Upload Plugin PHP Class 8.3.3
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

class Thumbs{
  
  protected
    $imageObjects = array(),
    $options = array(
      'mkdir_mode' => 0755,
      'user_dirs' => false,
      // Command or path for to the ImageMagick convert binary:
      'convert_bin' => 'convert',
      // Uncomment the following to add parameters in front of each
      // ImageMagick convert call (the limit constraints seem only
      // to have an effect if put in front):
      /*
      'convert_params' => '-limit memory 32MiB -limit map 32MiB',
      */
      // Set to 0 to use the GD library to scale and orient images,
      // set to 1 to use imagick (if installed, falls back to GD),
      // set to 2 to use the ImageMagick convert binary directly:
      'image_library' => 1,
      // Uncomment the following to define an array of resource limits
      // for imagick:
      /*
      'imagick_resource_limits' => array(
          imagick::RESOURCETYPE_MAP => 32,
          imagick::RESOURCETYPE_MEMORY => 32
      ),
      */
      'versions' => array(
        // The empty image version key defines options for the original image:
        '' => array(
          // Automatically rotate images based on EXIF meta data:
          'auto_orient' => true
        ),
        // Uncomment the following to create medium sized images:
        /*
        'medium' => array(
          'max_width' => 800,
          'max_height' => 600
        ),
        */
        'thumbnail' => array(
          // Uncomment the following to use a defined directory for the thumbnails
          // instead of a subdirectory based on the version identifier.
          // Make sure that this directory doesn't allow execution of files if you
          // don't pose any restrictions on the type of uploaded files, e.g. by
          // copying the .htaccess file from the files directory for Apache:
          //'dest_dir' => dirname($this->getServerVar('SCRIPT_FILENAME')).'/thumb/',
          // Uncomment the following to force the max
          // dimensions and e.g. create square thumbnails:
          //'crop' => true,
          'max_width' => 80,
          'max_height' => 80
        )
      )
    );

  public function __construct(array $options = array()){
    $this->options = array_merge($this->options, array(
      'dest_dir' => dirname($this->getServerVar('SCRIPT_FILENAME')).'/files/',
    ), $options);
  }

  public function scaledImage($filePath, $baseName = null){
    $successVersions = array();
    $failedVersions = array();
    if(!isset($baseName))
      $baseName = basename($filePath);
    $baseName = $this->cleanFileName($baseName);
    foreach($this->options['versions'] as $version => $options){
      $newFilePath = $this->getScaledImageFileDest($baseName, $version);
      if($this->createScaledImage($filePath, $newFilePath, $options)){
        $successVersions[$version] = $newFilePath;
      }else{
        $failedVersions[$version] = $newFilePath;
      }
    }
    $this->destroyImageObject($filePath);
    return array(
      'name' => $baseName,
      'success' => $successVersions,
      'failed' => $failedVersions,
    );
  }

  protected function getServerVar($id){

    return @$_SERVER[$id];
  }

  protected function destroyImageObject($filePath){
    if($this->options['image_library'] && extension_loaded('imagick')){
      return $this->imagickDestroyImageObject($filePath);
    }
  }

  protected function imagickDestroyImageObject($filePath){
    $image = (isset($this->imageObjects[$filePath])) ? $this->imageObjects[$filePath] : null ;
    return $image && $image->destroy();
  }

  protected function gdGetImageObject($filePath, $func, $noCache = false){
    if (empty($this->imageObjects[$filePath]) || $noCache){
      $this->gdDestroyImageObject($filePath);
      $this->imageObjects[$filePath] = $func($filePath);
    }
    return $this->imageObjects[$filePath];
  }

  protected function gdSetImageObject($filePath, $image) {
    $this->gdDestroyImageObject($filePath);
    $this->imageObjects[$filePath] = $image;
  }

  protected function gdDestroyImageObject($filePath){
    $image = (isset($this->imageObjects[$filePath])) ? $this->imageObjects[$filePath] : null ;
    return $image && imagedestroy($image);
  }

  protected function gdImageflip($image, $mode) {
    if (function_exists('imageflip')) {
      return imageflip($image, $mode);
    }
    $newWidth = $srcWidth = imagesx($image);
    $newHeight = $srcHeight = imagesy($image);
    $newImg = imagecreatetruecolor($newWidth, $newHeight);
    $srcX = 0;
    $srcY = 0;
    switch ($mode) {
      case '1': // flip on the horizontal axis
        $srcY = $newHeight - 1;
        $srcHeight = -$newHeight;
        break;
      case '2': // flip on the vertical axis
        $srcX  = $newWidth - 1;
        $srcWidth = -$newWidth;
        break;
      case '3': // flip on both axes
        $srcY = $newHeight - 1;
        $srcHeight = -$newHeight;
        $srcX  = $newWidth - 1;
        $srcWidth = -$newWidth;
        break;
      default:
        return $image;
    }
    imagecopyresampled(
      $newImg,
      $image,
      0,
      0,
      $srcX,
      $srcY,
      $newWidth,
      $newHeight,
      $srcWidth,
      $srcHeight
    );
    return $newImg;
  }

  protected function gdOrientImage($filePath, $srcImg) {
    if (!function_exists('exif_read_data')) {
      return false;
    }
    $exif = @exif_read_data($filePath);
    if ($exif === false) {
      return false;
    }
    $orientation = (int)@$exif['Orientation'];
    if ($orientation < 2 || $orientation > 8) {
      return false;
    }
    switch ($orientation) {
      case 2:
        $newImg = $this->gdImageflip(
          $srcImg,
          defined('IMG_FLIP_VERTICAL') ? IMG_FLIP_VERTICAL : 2
        );
        break;
      case 3:
        $newImg = imagerotate($srcImg, 180, 0);
        break;
      case 4:
        $newImg = $this->gdImageflip(
          $srcImg,
          defined('IMG_FLIP_HORIZONTAL') ? IMG_FLIP_HORIZONTAL : 1
        );
        break;
      case 5:
        $tmpImg = $this->gdImageflip(
          $srcImg,
          defined('IMG_FLIP_HORIZONTAL') ? IMG_FLIP_HORIZONTAL : 1
        );
        $newImg = imagerotate($tmpImg, 270, 0);
        imagedestroy($tmpImg);
        break;
      case 6:
        $newImg = imagerotate($srcImg, 270, 0);
        break;
      case 7:
        $tmpImg = $this->gdImageflip(
          $srcImg,
          defined('IMG_FLIP_VERTICAL') ? IMG_FLIP_VERTICAL : 2
        );
        $newImg = imagerotate($tmpImg, 270, 0);
        imagedestroy($tmpImg);
        break;
      case 8:
        $newImg = imagerotate($srcImg, 90, 0);
        break;
      default:
        return false;
    }
    $this->gdSetImageObject($filePath, $newImg);
    return true;
  }

  public function cleanFileName($baseName){
    return preg_replace('/[^_a-z0-9\.]/', '_', strtolower($baseName));
  }

  public function getScaledImageFileDest($baseName, $version = ''){
    $baseName = $this->cleanFileName($baseName);
    $versionDir = @$this->options['versions'][$version]['dest_dir'];
    if(!$versionDir){
      if(empty($version)){
        $versionDir = $this->options['dest_dir'];
      }else{
        $versionDir = $this->options['dest_dir'] . $version . '/';
      }
    }
    if(!is_dir($versionDir))
      mkdir($versionDir, $this->options['mkdir_mode'], true);

    $newFilePath = $versionDir . $baseName;

    return $newFilePath;
  }

  protected function imagickGetImageObject($filePath, $noCache = false){
    if (empty($this->imageObjects[$filePath]) || $noCache){
      $this->imagickDestroyImageObject($filePath);
      $image = new \Imagick();
      if (!empty($this->options['imagick_resource_limits'])){
        foreach ($this->options['imagick_resource_limits'] as $type => $limit){
          $image->setResourceLimit($type, $limit);
        }
      }
      $image->readImage($filePath);
      $this->imageObjects[$filePath] = $image;
    }
    return $this->imageObjects[$filePath];
  }

  protected function imagickSetImageObject($filePath, $image){
    $this->imagick_destroy_image_object($filePath);
    $this->imageObjects[$filePath] = $image;
  }

  protected function createScaledImage($fileName, $newFilePath, $options){
    if($this->options['image_library'] === 2){
      return $this->imagemagickCreateScaledImage($fileName, $newFilePath, $options);
    }
    if($this->options['image_library'] && extension_loaded('imagick')){
      return $this->imagickCreateScaledImage($fileName, $newFilePath, $options);
    }
    return $this->gdCreateScaledImage($fileName, $newFilePath, $options);
  }

  protected function imagemagickCreateScaledImage($filePath, $newFilePath, $options){
    $resize = @$options['max_width'].(empty($options['max_height']) ? '' : 'X'.$options['max_height']);
    if(!$resize && empty($options['auto_orient'])){
      if($filePath !== $newFilePath){
        return copy($filePath, $newFilePath);
      }
      return true;
    }
    $cmd = $this->options['convert_bin'];
    if(!empty($this->options['convert_params'])){
      $cmd .= ' '.$this->options['convert_params'];
    }
    $cmd .= ' '.escapeshellarg($filePath);
    if(!empty($options['auto_orient'])){
      $cmd .= ' -auto-orient';
    }
    if($resize){
      // Handle animated GIFs:
      $cmd .= ' -coalesce';
      if(empty($options['crop'])){
        $cmd .= ' -resize '.escapeshellarg($resize.'>');
      }else{
        $cmd .= ' -resize '.escapeshellarg($resize.'^');
        $cmd .= ' -gravity center';
        $cmd .= ' -crop '.escapeshellarg($resize.'+0+0');
      }
      // Make sure the page dimensions are correct (fixes offsets of animated GIFs):
      $cmd .= ' +repage';
    }
    if(!empty($options['convert_params'])){
      $cmd .= ' '.$options['convert_params'];
    }
    $cmd .= ' '.escapeshellarg($newFilePath);
    exec($cmd, $output, $error);
    if($error){
      error_log(implode('\n', $output));
      return false;
    }
    return true;
  }

  protected function imagickCreateScaledImage($filePath, $newFilePath, $options){
    $image = $this->imagickGetImageObject($filePath, !empty($options['no_cache']));
    if($image->getImageFormat() === 'GIF'){
      // Handle animated GIFs:
      $images = $image->coalesceImages();
      foreach ($images as $frame){
        $image = $frame;
        $this->imagickSetImageObject($filePath, $image);
        break;
      }
    }
    $imageOriented = false;
    if(!empty($options['auto_orient'])){
      $imageOriented = $this->imagick_orient_image($image);
    }
    $newWidth = $maxWidth = $imgWidth = $image->getImageWidth();
    $newHeight = $maxHeight = $imgHeight = $image->getImageHeight();
    if(!empty($options['max_width'])){
      $newWidth = $maxWidth = $options['max_width'];
    }
    if(!empty($options['max_height'])){
      $newHeight = $maxHeight = $options['max_height'];
    }
    if(!($imageOriented || $maxWidth < $imgWidth || $maxHeight < $imgHeight)){
      if($filePath !== $newFilePath){
        return copy($filePath, $newFilePath);
      }
      return true;
    }
    $crop = !empty($options['crop']);
    if($crop){
      $x = 0;
      $y = 0;
      if(($imgWidth / $imgHeight) >= ($maxWidth / $maxHeight)){
        $newWidth = 0; // Enables proportional scaling based on max_height
        $x = ($imgWidth / ($imgHeight / $maxHeight) - $maxWidth) / 2;
      }else{
        $newHeight = 0; // Enables proportional scaling based on max_width
        $y = ($imgHeight / ($imgWidth / $maxWidth) - $maxHeight) / 2;
      }
    }
    $success = $image->resizeImage(
      $newWidth,
      $newHeight,
      isset($options['filter']) ? $options['filter'] : \imagick::FILTER_LANCZOS,
      isset($options['blur']) ? $options['blur'] : 1,
      $newWidth && $newHeight // fit image into constraints if not to be cropped
    );
    if($success && $crop){
      $success = $image->cropImage(
        $maxWidth,
        $maxHeight,
        $x,
        $y
      );
      if($success){
        $success = $image->setImagePage($maxWidth, $maxHeight, 0, 0);
      }
    }
    $type = strtolower(substr(strrchr($filePath, '.'), 1));
    switch ($type){
      case 'jpg':
      case 'jpeg':
        if(!empty($options['jpeg_quality'])){
          $image->setImageCompression(\imagick::COMPRESSION_JPEG);
          $image->setImageCompressionQuality($options['jpeg_quality']);
        }
        break;
    }
    if(!empty($options['strip'])){
      $image->stripImage();
    }
    return $success && $image->writeImage($newFilePath);
  }

  protected function gdCreateScaledImage($filePath, $newFilePath, $options){
    if(!function_exists('imagecreatetruecolor')){
      error_log('Function not found: imagecreatetruecolor');
      return false;
    }
    $type = strtolower(substr(strrchr($filePath, '.'), 1));
    switch ($type){
      case 'jpg':
      case 'jpeg':
        $srcFunc = 'imagecreatefromjpeg';
        $writeFunc = 'imagejpeg';
        $imageQuality = isset($options['jpeg_quality']) ? $options['jpeg_quality'] : 75;
        break;
      case 'gif':
        $srcFunc = 'imagecreatefromgif';
        $writeFunc = 'imagegif';
        $imageQuality = null;
        break;
      case 'png':
        $srcFunc = 'imagecreatefrompng';
        $writeFunc = 'imagepng';
        $imageQuality = isset($options['png_quality']) ? $options['png_quality'] : 9;
        break;
      default:
        return false;
    }
    $srcImg = $this->gdGetImageObject(
      $filePath,
      $srcFunc,
      !empty($options['no_cache'])
    );
    $imageOriented = false;
    if(!empty($options['auto_orient']) && $this->gdOrientImage(
        $filePath,
        $srcImg
      )){
      $imageOriented = true;
      $srcImg = $this->gdGetImageObject(
        $filePath,
        $srcFunc
      );
    }
    $maxWidth = $imgWidth = imagesx($srcImg);
    $maxHeight = $imgHeight = imagesy($srcImg);
    if(!empty($options['max_width'])){
      $maxWidth = $options['max_width'];
    }
    if(!empty($options['max_height'])){
      $maxHeight = $options['max_height'];
    }
    $scale = min(
      $maxWidth / $imgWidth,
      $maxHeight / $imgHeight
    );
    if($scale >= 1){
      if($imageOriented){
        return $writeFunc($srcImg, $newFilePath, $imageQuality);
      }
      if($filePath !== $newFilePath){
        return copy($filePath, $newFilePath);
      }
      return true;
    }
    if(empty($options['crop'])){
      $newWidth = $imgWidth * $scale;
      $newHeight = $imgHeight * $scale;
      $dstX = 0;
      $dstY = 0;
      $newImg = imagecreatetruecolor($newWidth, $newHeight);
    }else{
      if(($imgWidth / $imgHeight) >= ($maxWidth / $maxHeight)){
        $newWidth = $imgWidth / ($imgHeight / $maxHeight);
        $newHeight = $maxHeight;
      }else{
        $newWidth = $maxWidth;
        $newHeight = $imgHeight / ($imgWidth / $maxWidth);
      }
      $dstX = 0 - ($newWidth - $maxWidth) / 2;
      $dstY = 0 - ($newHeight - $maxHeight) / 2;
      $newImg = imagecreatetruecolor($maxWidth, $maxHeight);
    }
    // Handle transparency in GIF and PNG images:
    switch ($type){
      case 'gif':
      case 'png':
        imagecolortransparent($newImg, imagecolorallocate($newImg, 0, 0, 0));
      case 'png':
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
        break;
    }
    $success = imagecopyresampled(
      $newImg,
      $srcImg,
      $dstX,
      $dstY,
      0,
      0,
      $newWidth,
      $newHeight,
      $imgWidth,
      $imgHeight
    ) && $writeFunc($newImg, $newFilePath, $imageQuality);
    $this->gdSetImageObject($filePath, $newImg);
    return $success;
  }

}