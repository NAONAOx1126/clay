<?php
/**
 * 規定の矩形に収まるように縮小処理を行う。
 */
abstract class ImageFilter_BaseFilter{
	var $width;
	
	var $height;
	
	/**
	 * 処理後の画像の幅を取得する。
	 */
	function getWidth(){
		return $this->width;
	}
	
	/**
	 * 処理後の画像の高さを取得する。
	 */
	function getHeight(){
		return $this->height;
	}
	
	/**
	 * 透過処理済みの新しいイメージオブジェクトを生成する。
	 */
	function transparent($image, $info, $newImage = null){
		if($newImage == null){
			$newImage = imagecreatetruecolor($this->width, $this->height);
		}
		if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
			// 元画像の透過色を取得する。
			$trnprt_indx = imagecolortransparent($image);
	
			// 透過色が設定されている場合は透過処理を行う。
			if ($trnprt_indx >= 0) {
				// カラーインデックスから透過色を取得する。
				$trnprt_color = imagecolorsforindex($image, $trnprt_indx);
	
				// 取得した透過色から変換後の画像用のカラーインデックスを生成
				$trnprt_indx = imagecolorallocate($newImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
	
				// 生成した透過色で変換後画像を塗りつぶし
				imagefill($newImage, 0, 0, $trnprt_indx);
	
				// 生成した透過色を変換後画像の透過色として設定
				imagecolortransparent($newImage, $trnprt_indx);
			} elseif ($info[2] == IMAGETYPE_PNG) {
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
		return $newImage;
	}
	
	abstract function filter($image, $info);
}
?>
