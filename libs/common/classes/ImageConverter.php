<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */

/**
 * 画像の処理を行うクラスです。
 *
 * @package ImageConverter
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class ImageConverter
{
	var $info = null;

	var $image = null;

    /**
     * コンストラクタです。
     *
     * @access public
     */
    function __construct($file)
    {
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
	
	/**
	 * 指定した位置にTrueType Fontのテキストを配置する。
	 */
	function setTrueText($x, $y, $font, $size, $text, $center_w = 0, $center_h = 0){
		$bbox = imageftbbox($size, 0, $font, $text);
		if($center_w > 0){
			$x = $x + ($center_w - $bbox[4] + $bbox[0]) / 2;
		}
		if($center_h > 0){
			$y = $y + ($center_h - $bbox[5] + $bbox[1]) / 2;
		}
		$y = $y + ($bbox[1] - $bbox[5]);
		$black = imagecolorallocate($this->image, 0, 0, 0);
		imagefttext($this->image, $size, 0, $x, $y, $black, $font, $text);
	}
	
	/**
	 * 指定した位置にテキストを配置する。
	 */
	function setText($x, $y, $font, $size, $text){
		$black = imagecolorallocate($this->image, 0, 0, 0);
		imagestring($this->image, 5, $x, $y, $text, $black);
	}
	
	/**
	 * 指定した位置に画像を配置する。
	 */
	function setImage($x, $y, $image, $center_w = 0, $center_h = 0){
		if($center_w > 0){
			$x = $x + ($center_w - $image->info[0]) / 2;
		}
		if($center_h > 0){
			$y = $y + ($center_h - $image->info[1]) / 2;
		}
		imagecopy($this->image, $image->image, $x, $y, 0, 0, $image->info[0], $image->info[1]);
	}
	
	/**
	 * 縮小してサイズをあわせるメソッドです。
	 * 指定した枠の長さが短辺に合わさるように縮小されます。
	 */
	function resizeShort($width, $height, $enlarge = true){
		if($this->info[0] < $this->info[1]){
			// 幅が短い場合
			if($enlarge || $width < $this->info[0]){
				// 拡大ありか、指定幅が現在幅より大きい場合、処理を行う。
				$height = $this->info[1] * $width / $this->info[0];
				$this->resize($width, $height, $enlarge);
			}
		}else{
			// 高さが短い場合
			if($enlarge || $height < $this->info[1]){
				// 拡大ありか、指定高さが現在高さより大きい場合、処理を行う。
				$width = $this->info[0] * $height / $this->info[1];
				$this->resize($width, $height, $enlarge);
			}
		}
	}
	
	/**
	 * 縮小してサイズをあわせるメソッドです。
	 * 指定した枠の長さが長辺に合わさるように縮小されます。
	 */
	function resizeLong($width, $height, $enlarge = true){
		if($this->info[1] < $this->info[0]){
			// 幅が短い場合
			if($enlarge || $width < $this->info[0]){
				// 拡大ありか、指定幅が現在幅より大きい場合、処理を行う。
				$height = $this->info[1] * $width / $this->info[0];
				$this->resize($width, $height, $enlarge);
			}
		}else{
			// 高さが短い場合
			if($enlarge || $height < $this->info[1]){
				// 拡大ありか、指定高さが現在高さより大きい場合、処理を行う。
				$width = $this->info[0] * $height / $this->info[1];
				$this->resize($width, $height, $enlarge);
			}
		}
	}
	
    /**
     * 縮小してサイズをあわせるメソッドです。
	 * 値に０を入れた場合、もう一辺の縮小比に併せて縮小されます。
	 * 
	 * @access public
     */
    function resize($width, $height, $enlarge = true)
    {
		if($width > 0 || $height > 0){
			// 変形後のサイズを自動計算
			if($width == 0){
				$width = floor($this->info[0] * $height / $this->info[1]);
			}
			if($height == 0){
				$height = floor($this->info[1] * $width / $this->info[0]);
			}
			
			// 拡大有りにするか、拡大要素が無い場合、変形処理を実行
			if($enlarge || ($width < $this->info[0] && $height < $this->info[1])){
				$newImage = imagecreatetruecolor($width, $height);
	
				// 透過処理
				if ( ($this->info[2] == IMAGETYPE_GIF) || ($this->info[2] == IMAGETYPE_PNG) ) {
					// 元画像の透過色を取得する。
					$trnprt_indx = imagecolortransparent($this->image);
			
					// 透過色が設定されている場合は透過処理を行う。
					if ($trnprt_indx >= 0) {
						// カラーインデックスから透過色を取得する。
						$trnprt_color    = imagecolorsforindex($this->image, $trnprt_indx);
			
						// 取得した透過色から変換後の画像用のカラーインデックスを生成
						$trnprt_indx    = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
			
						// 生成した透過色で変換後画像を塗りつぶし
						imagefill($newImage, 0, 0, $trnprt_indx);
			
						// 生成した透過色を変換後画像の透過色として設定
						imagecolortransparent($newImage, $trnprt_indx);
					} elseif ($this->info[2] == IMAGETYPE_PNG) {
						// アルファブレンディングをOFFにする。
						imagealphablending($newImage, false);
						
						// アルファブレンドのカラーを作成する。
						$trnprt_indx = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
						
						// 生成した透過色で変換後画像を塗りつぶし
						imagefill($newImage, 0, 0, $trnprt_indx);
						
						// 透過色をGIF用に設定
						imagecolortransparent($newImage, $trnprt_indx);
			
						// 生成した透過色を変換後画像の透過色として設定
						imagesavealpha($newImage, true);
					}
				}
	
				// 画像縮小処理
				imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, $this->info[0], $this->info[1]);
				imagedestroy($this->image);
				$this->image = $newImage;
				$this->info[0] = $width;
				$this->info[1] = $height;
			}
		}
	}

    /**
     * 画像を適当なサイズに切り取るメソッドです。
	 * 値に０を入れた場合、もう一辺の縮小比に併せて縮小されます。
	 * 
	 * @access public
     */
    function trim($width, $height)
    {
		if($width > 0 || $height > 0){
			// 変形後のサイズを自動計算
			if($width == 0 || $this->info[0] < $width){
				$width = $this->info[0];
			}
			if($height == 0 || $this->info[1] < $height){
				$height = $this->info[1];
			}
			
			// トリムする領域がある場合に処理を実行
			if($width < $this->info[0] || $height < $this->info[1]){
				$newImage = imagecreatetruecolor($width, $height);
	
				// 透過処理
				if ( ($this->info[2] == IMAGETYPE_GIF) || ($this->info[2] == IMAGETYPE_PNG) ) {
					// 元画像の透過色を取得する。
					$trnprt_indx = imagecolortransparent($this->image);
			
					// 透過色が設定されている場合は透過処理を行う。
					if ($trnprt_indx >= 0) {
						// カラーインデックスから透過色を取得する。
						$trnprt_color    = imagecolorsforindex($this->image, $trnprt_indx);
			
						// 取得した透過色から変換後の画像用のカラーインデックスを生成
						$trnprt_indx    = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
			
						// 生成した透過色で変換後画像を塗りつぶし
						imagefill($newImage, 0, 0, $trnprt_indx);
			
						// 生成した透過色を変換後画像の透過色として設定
						imagecolortransparent($newImage, $trnprt_indx);
					} elseif ($this->info[2] == IMAGETYPE_PNG) {
						// アルファブレンディングをOFFにする。
						imagealphablending($newImage, false);
						
						// アルファブレンドのカラーを作成する。
						$trnprt_indx = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
						
						// 生成した透過色で変換後画像を塗りつぶし
						imagefill($newImage, 0, 0, $trnprt_indx);
						
						// 透過色をGIF用に設定
						imagecolortransparent($newImage, $trnprt_indx);
			
						// 生成した透過色を変換後画像の透過色として設定
						imagesavealpha($newImage, true);
					}
				}
	
				// 画像縮小処理
				imagecopy($newImage, $this->image, 0, 0, floor(($this->info[0] - $width) / 2), floor(($this->info[1] - $height) / 2), $width, $height);
				imagedestroy($this->image);
				$this->image = $newImage;
				$this->info[0] = $width;
				$this->info[1] = $height;
			}
		}		
	}
	
	function save($file){
		// 透過処理
		if ( ($this->info[2] == IMAGETYPE_GIF) || ($this->info[2] == IMAGETYPE_PNG) ) {
			// 元画像の透過色を取得する。
			$trnprt_indx = imagecolortransparent($this->image);
	
			// 透過色が設定されている場合は透過処理を行う。
			if ($trnprt_indx < 0 && $this->info[2] == IMAGETYPE_PNG) {
				// アルファブレンディングをOFFにする。
				imagealphablending($this->image, false);
				
				// 生成した透過色を変換後画像の透過色として設定
				imagesavealpha($this->image, true);
			}
		}
		switch($this->info[2]){
			case IMAGETYPE_GIF:
				imagegif($this->image, $file);
				break;
			case IMAGETYPE_JPEG:
			case IMAGETYPE_JPEG2000:
				imagejpeg($this->image, $file, 100);
				break;
			case IMAGETYPE_PNG:
				imagepng($this->image, $file);
				break;
			default:
				break;
		}
	}
}
?>