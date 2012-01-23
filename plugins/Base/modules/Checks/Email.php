<?php
/**
 * ### Base.Checks.Email
 * メールアドレスかどうかのチェックを行うCheckパッケージのクラスです。
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
class Default_Checks_Email extends FrameworkModule{
	/**
	 * モジュールのエンドポイント
	 */
	function execute($params){
		if(!is_array($_SERVER["ERRORS"])){
			$_SERVER["ERRORS"] = array();
		}
		if(!empty($_SERVER["POST"][$params->get("key")])){
			if(preg_match("/^[a-zA-Z0-9!$&*.=^`|~#%'+\\/?_{}-]+@([a-zA-Z0-9_-]+\\.)+[a-zA-Z]{2,4}$/", $_SERVER["POST"][$params->get("key")]) == 0){
				$_SERVER["ERRORS"][$params->get("key")] = $params->get("value").$params->get("suffix", "は正しいメールアドレスではありません。");
			}
		}
	}
}
?>
