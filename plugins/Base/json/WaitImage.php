<?php
/**
 * 画像が存在する場合のみサイズ調整をして画像のパスを返す処理を実行する。
 * 
 * @category  JSON
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 * 
 * 使い方：以下のようにすると、規定の画像が存在する場合のみ縮小画像で置き換える。
 * function thumbnail(image, width, height){
 * 	jQuery.ajax({
 *		url: "jsonp.php?p=Base.WaitImage&img=" + image + "&width=" + width + "&height=" + height, 
 *		dataType: "jsonp", 
 * 		success: function(data, status){
 *			var tags = "";
 *			if(data.image != ""){
 *				tags += "<img src=\"" + data.image + "\" />";
 *				$("#site_image").html(tags);
 *			}else{
 *				setTimeout("thumbnail('" + image + "', '" + width + "', '" + height + "')", 5000);
 *			}
 *		}
 *	});
 * }
 */
// 規定画像幅／高さを取得する。
$width = $_POST["width"];
$height = $_POST["height"];

// 画像のパスを取得する。
$image = str_replace(MINES_URL_BASE, MODULE_HOME.$_SERVER["USER_TEMPLATE"], $_POST["img"]);

if(file_exists($image)){
	list($w, $h) = getimagesize($image);
	if($width < $w || $height < $h){
		// 画像縮小処理
		$upload_file = $image;
		// 元画像の高さが幅を調整したあとの高さよりも高い場合は切り詰め、低い場合は調整後の画像サイズを切り詰める
		if($h > floor($w * $height / $width)){
			$h = floor($w * $height / $width);
		}else{
			$height = floor($width * $h / $w);
		}

		// 画像の幅を合わせるために縮小する。
		$image = imagecreatefromjpeg($upload_file);
		$image_p = imagecreatetruecolor($width, $height);
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $w, $h);
		// 画像に黒枠を設定
		$black = imagecolorallocate($image_p, 0, 0, 0);
		imagerectangle($image_p, 0, 0, $width - 1, $height - 1, $black);
		
		// 縮小画像を保存
		imagejpeg($image_p, $upload_file, 100);
		$image = $upload_file;
	}
	$result = array("image" => $_POST["img"]);
}else{
	// 画像が無い場合は仮の画像を割り当て
	$result = array("image" => "");
}
?>
