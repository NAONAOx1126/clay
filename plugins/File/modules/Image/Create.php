<?php
LoadModel("ImageModel", "File");
LoadModel("ImageContentModel", "File");

/**
 * 半角英数かどうかのチェックを行うCheckパッケージのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Check
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

class File_Image_Create extends FrameworkModule{
	function execute($params){
		if($params->check("key")){
			// 画像設定を取得
			$image = new ImageModel();
			$image->findByImageCode($params->get("key"));
			if(!empty($image->image_id)){
				// 画像コンテンツ設定を取得
				$imageContent = new ImageContentModel();
				$imageContents = $imageContent->getCotentArrayByImage($image->image_id);
				
				// テンプレートパスを取得
				$tpl_path = MODULE_HOME.$_SERVER["USER_TEMPLATE"].DS.$image->template_path;
				$output = new ImageConverter($tpl_path);
				
				foreach($imageContents as $content){
					if(empty($content->location_x)){
						$content->location_x = 0;
					}
					if(empty($content->location_y)){
						$content->location_y = 0;
					}
					if(!empty($content->font)){
						// テキストの場合
						if(empty($content->size)){
							$content->size = "10";
						}
						// フォントパスを取得
						$font_path = MINES_HOME.DS."fonts".DS.$content->font;
						// テキストコンテンツを取得
						$contentKeys = explode(".", $content->content_key);
						$text = $_SERVER["ATTRIBUTES"];
						foreach($contentKeys as $key){
							if(strpos($key, "@") !== FALSE){
								list($key, $mod) = explode("@", $key);
								if(strpos($mod, ":") !== FALSE){
									list($mod, $param) = explode(":", $mod);
								}
							}
							if(is_array($text)){
								$text = $text[$key];
							}elseif(is_object($text)){
								$text = $text->$key;
							}
							switch($mod){
								case "format":
									$text = sprintf($param, $text);
									break;
							}
						}
						// テキストを配置
						$output->setTrueText($content->location_x, $content->location_y, $font_path, $content->size, $text, $content->center_w, $content->center_h);
					}else{
						// 画像の場合
						
						// 画像パスを取得
						$contentKeys = explode(".", $content->content_key);
						$text = $_SERVER["ATTRIBUTES"];
						foreach($contentKeys as $key){
							if(is_array($text)){
								$text = $text[$key];
							}elseif(is_object($text)){
								$text = $text->$key;
							}
						}
						// テキストを配置
						$overImage = new ImageConverter(MODULE_HOME.$_SERVER["USER_TEMPLATE"].$text);
						$output->setImage($content->location_x, $content->location_y, $overImage, $content->center_w, $content->center_h);
					}
				}
				$extension = substr($image->template_path, strrpos($image->template_path, "."));
				$text = $_SERVER["ATTRIBUTES"];
				if($params->check("signature")){
					$sigKeys = explode(".", $params->get("signature"));
					foreach($sigKeys as $key){
						if(is_array($text)){
							$text = $text[$key];
						}elseif(is_object($text)){
							$text = $text->$key;
						}
					}
				}else{
					$text = uniqid();
				}
				$output_url = DS."upload/".$image->image_code."_".$text.$extension;
				$output_file = MODULE_HOME.$_SERVER["USER_TEMPLATE"].$output_url;
				$output->save($output_file);
				$_SERVER["ATTRIBUTES"][$params->get("result", "image")] = $output_url;
			}
		}
	}
}
?>
