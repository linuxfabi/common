<?php
class Image {
    
    private $path  = '';
    private $img   = false;
    public $width  = 0;
    public $height = 0;
    public $format = 0;
    
    
    
    function __construct($path) {
        if (!file_exists($path)) $this->__destruct();
        $this->path = $path;
        $this->img  = $this->loadImage($this->path);
    }
    
    function __destruct() {
        if (is_resource($this->img)) imageDestroy($this->img);
    }
    
    
    
    private function loadImage() {
        $info = getImageSize($this->path);
        $this->width  = $info[0];
        $this->height = $info[1];
        $this->format = $info[2];
        
        switch($this->format) {
            case IMAGETYPE_GIF:  return imagecreatefromgif($this->path);
            case IMAGETYPE_PNG:  $img = imageCreateFromPng($this->path);
                                 imageAlphaBlending($img, true);
                                 imageSaveAlpha($img, true);
                                 return $img;
            case IMAGETYPE_JPEG: return imagecreatefromjpeg($this->path);
        }
        return false;
    }
    public function saveImage($savePath = '') {
        if (empty($savePath)) $savePath = $this->path;
        switch($this->format) {
            case IMAGETYPE_GIF:  imagegif($this->img, $savePath);  break;
            case IMAGETYPE_PNG:  imagepng($this->img, $savePath);  break;
            case IMAGETYPE_JPEG: imagejpeg($this->img, $savePath); break;
        }
        return false;
    }
    public function sendHeader() {
        switch($this->format) {
            case IMAGETYPE_GIF:  header("Content-Type: Image/gif");  break;
            case IMAGETYPE_PNG:  header("Content-Type: Image/png");  break;
            case IMAGETYPE_JPEG: header("Content-Type: Image/jpeg"); break;
        }
        return false;
    }
    public function showImage() {
        switch($this->format) {
            case IMAGETYPE_GIF:  imagegif($this->img);  break;
            case IMAGETYPE_PNG:  imagepng($this->img);  break;
            case IMAGETYPE_JPEG: imagejpeg($this->img); break;
        }
        return false;
    }
    public function getFileExt() {
        switch($this->format) {
            case IMAGETYPE_GIF:  return 'gif';
            case IMAGETYPE_PNG:  return 'png';
            case IMAGETYPE_JPEG: return 'jpg';
        }
        return false;
    }
    
    
    
    
    public function resizeImage($newWidth, $newHeight) {
        $tImg = imagecreatetruecolor($newWidth, $newHeight);
        imagealphablending($tImg, true);
        imagesavealpha($tImg, true);
        imagefill($tImg, 0, 0, 0x7fff0000);
        imagecopyresampled($tImg, $this->img, 
                           0, 0, 0, 0, 
                           $newWidth, $newHeight, 
                           $this->width, $this->height);
        return $tImg;
    }
    
    public function rotateImage($angle) {
        imageRotate($this->img, $angle, 0x7fff0000);
    }
     
    public function insertImage($iImg, $iWidth, $iHeight, $oX, $oY) {
        imagecopyresampled($this->img, $iImg, 
                           $oX, $oY, 0, 0, 
                           $iWidth, $iHeight, 
                           $iWidth, $iHeight);
        return $this->img;
    }
    
    public function insertText($text, $font, $x, $y, $color) {
        imagestring($this->img, $font, $x, $y, $text, $color);
    }
    
    public function insertTTFText($text, $color, $ttf, $x, $y, $size) {
        $dims  = imagettfbbox($size, 0, $ttf, $text);
        /*$up    = abs($dims[7]);
        $down  = abs($dims[1]);
        $left  = abs($dims[0]);
        $right = abs($dims[2]);
        
        $height = abs($dims[5] - $dims[1]);
        
        $y = ($y + $up);
        */
        /*
        $box    = @imageTTFBbox($size, 0, $ttf, $text); 
        $width  = abs($box[4] - $box[0]); 
        $height = abs($box[5] - $box[1]);
        $x -= $width/2; 
        $y += $heigth/2;
        */
        
        
        imagettftext ($this->img, $size, 0, $x, $y+abs($dims[7]), $color, $ttf, $text);
        return $this->img;
    }
    
    public function rgbToColor($r, $g, $b) {
        return imageColorAllocate($this->img, $r, $g, $b);
    }
}
?>