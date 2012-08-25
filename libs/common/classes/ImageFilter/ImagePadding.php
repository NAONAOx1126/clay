<?php
/**
 * 規定の矩形に収まるように縮小処理を行う。
 */
class ImageFilter_ImagePadding extends ImageFilter_ImageResize{
	var $background;
	
	function __construct($width, $height, $background = array(255, 255, 255)){
		$this->width = $width;
		$this->height = $height;
		$this->background = $background;
	}
	
	function filter($image, $info){
		if($this->width > 0 || $this->height > 0){
			// 調整前の幅と高さを取得する
			$baseWidth = $this->width;
			$baseHeight = $this->height;
			
			// 変形後の幅と高さを計算する。
			$this->calculateSize($info);
			
			// 調整後の幅と高さを取得する
			$targetWidth = $this->width;
			$targetHeight = $this->height;
			
			// 調整前の幅と高さが0の時は調整後の値を使う
			if($baseWidth == 0){
				$baseWidth = $targetWidth;
			}
			if($baseHeight == 0){
				$baseHeight = $targetHeight;
			}
			
			// 画像は調整前の高さで作成するため、置き換える
			$this->width = $baseWidth;
			$this->height = $baseHeight;
			
			// 透過処理済みの新しい画像オブジェクトを生成
			$newImage = $this->transparent($image, $info);
			
			// 背景部分を指定色で塗りつぶし
			imagefill($newImage, 0, 0, imagecolorallocate($newImage, $this->background[0], $this->background[1], $this->background[2]));
	
			// 画像縮小処理
			imagecopyresampled($newImage, $image, floor(($baseWidth - $targetWidth) / 2), floor(($baseHeight - $targetHeight) / 2), 0, 0, $targetWidth, $targetHeight, $info[0], $info[1]);
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
?>
