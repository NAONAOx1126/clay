<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * 画像に対して重ねあわせの処理を行う。
 * 位置を指定しない場合には中央になるように配置する。
 *
 * @package Filter
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Filter_Image_Overlay extends Clay_Filter_Image_Base{
	var $resize;
	
	var $info;
	
	var $image;
	
	function __construct($file, $resize = false){
		$this->resize = $resize;
		$this->info = getimagesize($file);
		switch($this->info[2]){
			case IMAGETYPE_GIF:
				$this->image = imagecreatefromgif($file);
				break;
			case IMAGETYPE_JPEG:
			case IMAGETYPE_JPEG2000:
				$this->image = imagecreatefromjpeg($file);
				break;
			case IMAGETYPE_PNG:
				$this->image = imagecreatefrompng($file);
				break;
			default:
				break;
		}
	}
	
	function filter($image, $info){
		// 拡大縮小はしないため、幅と高さは元画像のものを使用する。
		$this->width = $info[0];
		$this->height = $info[1];
		
		$newHeight = $this->info[1];
		$newWidth = $this->info[0];
		
		if($this->resize){
			// 幅が規定値より大きい場合は調整する。
			if($info[0] < $this->info[0]){
				$newHeight = floor($info[0] * $this->info[1] / $this->info[0]);
				$newWidth = $info[0];
			}
			
			// 高さが規定値より大きい場合は調整する。
			if($info[1] < $newHeight){
				$newWidth = floor($info[1] * $newWidth / $newHeight);
				$newHeight = $info[1];
			}
		}
		
		// 画像重ね合わせ処理
		imagecopyresampled($image, $this->image, floor(($info[0] - $newWidth) / 2), floor(($info[1] - $newHeight) / 2), 0, 0, $newWidth, $newHeight, $this->info[0], $this->info[1]);
		
		return $image;
	}
}
 