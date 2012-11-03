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
 * 規定の矩形に収まるように縮小処理を行う。
 *
 * @package Filter
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Filter_Image_Resize extends Clay_Filter_Image_Base{
	function __construct($width, $height){
		$this->width = $width;
		$this->height = $height;
	}
	
	function calculateSize($info){
		// 一方の辺指定が0の場合、比率を維持した時のサイズを設定する。
		if($this->width == 0){
			$this->width = floor($info[0] * $this->height / $info[1]);
		}
		if($this->height == 0){
			$this->height = floor($info[1] * $this->width / $info[0]);
		}
		
		// 幅が規定値より大きい場合は調整する。
		if($this->width < $info[0] && floor($this->width * $info[1] / $info[0]) < $this->height){
			$this->height = floor($this->width * $info[1] / $info[0]);
		}
		
		// 高さが規定値より大きい場合は調整する。
		if($this->height < $info[1] && floor($this->height * $info[0] / $info[1]) < $this->width){
			$this->width = floor($this->height * $info[0] / $info[1]);
		}
		if($info[0] < $this->width && $info[1] < $this->height){
			$this->width = $info[0];
			$this->height = $info[1];
		}
	}
	
	function filter($image, $info){
		if($this->width > 0 || $this->height > 0){
			// 変形後の幅と高さを計算する。
			$this->calculateSize();
			
			// 透過処理済みの新しい画像オブジェクトを生成
			$newImage = $this->transparent($image, $info);
	
			// 画像縮小処理
			imagecopyresampled($newImage, $image, 0, 0, 0, 0, $this->width, $this->height, $info[0], $info[1]);
			imagedestroy($image);
			$image = $newImage;
		}else{
			// 処理をしなかった場合は元の幅と高さを返す
			$this->width = $info[0];
			$this->height = $info[1];
		}
		return $image;
	}
}
 