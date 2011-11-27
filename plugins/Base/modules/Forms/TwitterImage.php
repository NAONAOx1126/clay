<?php
/**
 * ### Base.Forms.TwitterImage
 * Twitter画像をフォームにインポートするモジュールです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param image POSTに引き継ぐためのキー名
 */
class Base_Forms_TwitterImage extends FrameworkModule{
	function execute($params){
		// twitterとimageのパラメータは必須
		if($params->check("image") && !empty($_SERVER["Twitter"]["user"]->profile_image_url)){
			$image = $params->get("image");
			
			// 画像のデータが未登録で、twitterアカウントが設定されている場合には、画像データにtwitterの画像
			$_POST[$image] = $_SERVER["Twitter"]["user"]->profile_image_url;
		}
	}
}
?>
