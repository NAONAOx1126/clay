<?php
/**
 * ### Base.Pages.BasicAuth
 * 簡易的にページ単位のBasic認証を設定するためのモジュールです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param login Basic認証のログインID
 * @param password Basic認証のパスワード
 * @param text 認証ダイアログのメッセージ
 * @param error エラー時のメッセージ
 */
class Base_Pages_BasicAuth extends FrameworkModule{
	function execute($params){
		if($params->check("login") && $params->check("password")){
			if($_SERVER["PHP_AUTH_USER"] != $params->get("login") || $_SERVER["PHP_AUTH_PW"] != $params->get("password")) {
				header("WWW-Authenticate: Basic realm=\"".$params->get("text", "Please Enter Your Password")."\"");
				header("HTTP/1.0 401 Unauthorized");
				//キャンセル時の表示
				echo $params->get("error", "Authorization Required");
				exit;
			}
		}
	}
}
?>
