<?php
class cropImage{
	var $imgSrc,$myImage,$cropHeight,$cropWidth,$x,$y,$thumb; 
	function setImage($image){
	   $this->imgSrc = $image; 
	                     
	   list($width, $height) = getimagesize($this->imgSrc); 
	   //thay lan luot o day
	   $this->myImage = imagecreatefromjpeg($this->imgSrc) or die("Error: Cannot find image!"); 
	            
	       if($width > $height) $biggestSide = $width; //find biggest length
	       else $biggestSide = $height; 
	                     
	   $cropPercent = 0.5; // This will zoom in to 50% zoom (crop)
	   $this->cropWidth   = $biggestSide*$cropPercent; 
	   $this->cropHeight  = $biggestSide*$cropPercent; 
	                     
	                     
	   $this->x = ($width-$this->cropWidth)/2;
	   $this->y = ($height-$this->cropHeight)/2;
	             
	}
	function createThumb($thumbSize, $cropx, $cropy){
	                    
	  $this->thumb = imagecreatetruecolor($cropx, $cropy); 
	
	  imagecopyresampled($this->thumb, $this->myImage, 0, 0,$this->x, $this->y, $thumbSize, $thumbSize, $this->cropWidth, $this->cropHeight); 
	}  
	function renderImage($filename){
	   imagejpeg($this->thumb, $filename,'90');
	   imagedestroy($this->thumb); 
	}
}
?>