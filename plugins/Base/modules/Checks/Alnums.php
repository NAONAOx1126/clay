<?php
/**
 * ### Base.Checks.Alnums
 * 半角英数かどうかのチェックを行うCheckパッケージのクラスです。
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
class Base_Checks_Alnums extends FrameworkModule{
	/**
	 * モジュールのエンドポイント
	 */
	function execute($params){
		if(!is_array($_SERVER["ERRORS"])){
			$_SERVER["ERRORS"] = array();
		}
		if(!ctype_alnum($_SERVER["POST"][$params->get("key")])){
			$_SERVER["ERRORS"][$params->get("key")] = $params->get("value").$params->get("suffix", "は半角英数字で入力してください。");
		}
	}
}
?>
