<?php
/**
 * ### Base.Checks.ContentValidate
 * コンテンツに正規表現が含まれるかどうかのチェックを行うCheckパッケージのクラスです。
 *
 * @category  Modules
 * @package   Checks
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 * @param key チェック対象のキー名
 * @param value チェック対象の名称
 * @param suffix エラーメッセージのサフィックス
 */
class Default_Checks_ContentValidate extends FrameworkModule{
	function execute($params){
		if($params->check("key")){
			if(!is_array($_SERVER["ERRORS"])){
				$_SERVER["ERRORS"] = array();
			}
			
			// サイトのコンテンツを取得
			$text = $_SERVER["POST"][$params->get("key")];
			if(!empty($text)){
				// テキストに正規表現が含まれているかどうかチェック
				if(preg_match($params->get("regex"), $text, $p) > 0){
					$_SERVER["URL_PARAMS"][$params->get("key")] = $p;
				}else{
					$_SERVER["ERRORS"][$params->get("key")] = $params->get("value").$params->get("suffix", "は正しくありません。");
				}
			}
		}
	}
}
?>
