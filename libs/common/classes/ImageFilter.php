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

// フィルタを読み込み
require(FRAMEWORK_CLASS_LIBRARY_HOME."/ImageFilter/BaseFilter.php");
require(FRAMEWORK_CLASS_LIBRARY_HOME."/ImageFilter/ImageOverlay.php");
require(FRAMEWORK_CLASS_LIBRARY_HOME."/ImageFilter/ImageResize.php");
require(FRAMEWORK_CLASS_LIBRARY_HOME."/ImageFilter/ImagePadding.php");

/**
 * 画像の処理を行うクラスです。
 *
 * @package ImageFilter
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class ImageFilter
{
	var $info = null;

	var $image = null;

    /**
     * コンストラクタです。
     *
     * @access public
     */
    function __construct($file){
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
    
    function addFilter($filter){
    	// フィルタ処理の実行
    	$this->image = $filter->filter($this->image, $this->info);
    	
    	// 処理実行後の幅と高さを取得
    	$this->info[0] = $filter->getWidth();
    	$this->info[1] = $filter->getHeight();
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