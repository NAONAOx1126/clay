<?php
/**
 * イプシロン決済登録用のモジュールクラスです。
 *
 * PHP5.3以上での動作のみ保証しています。<br>
 * 動作自体はPHP5.2以上から動作します。<br>
 *
 * @category  Modules
 * @package   Shopping
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

LoadModel("Setting", "Members");

/**
 * イプシロン決済登録用のモジュールクラスです。
 * 
 * <p>
 * 以下のような書式でメタタグをテンプレートに貼付けることで呼び出せます。<br>
 * 結果はテンプレートで$ATTRIBUTES.xxxxxの形式で取得出来ます。<br>
 * xxxxxはresultパラメータで使用した値が使われます。<br>
 * resultパラメータが無い場合はこのモジュールで取得出来る結果はありません。
 * </p>
 * <samp>
 * <meta name="loadmodule" content="Shopping.Shopping.ZeroComplete" [mode="add"] />
 * </samp>
 * - mode : このモジュールの処理を反応させるために必要なPOSTパラメータの名前
 *
 * @package Shopping
 * @author Naohisa Minagawa <info@sweetberry.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Members_ZeroComplete extends FrameworkModule{
	function execute($params){
		// ゼロ決済からのアドレスの場合のみ処理する。
		//if($_SERVER["REMOTE_ADDR"] == "210.164.6.67" || $_SERVER["REMOTE_ADDR"] == "202.221.139.50"){
			// 設定したクライアントIPが一致して決済結果がokの場合のみ処理する。
			if($params->get("client") == $_POST["clientip"] && $_POST[$params->get("result_key")] == $params->get("result_value")){
				// 認証の顧客IDと受信した顧客IDが一致するかチェックする。
				if($_SESSION[CUSTOMER_SESSION_KEY]->customer_id == $_POST["sendid"]){
					// 一致した場合は、POSTにmodeと値を設定
					list($key, $value) = explode("-", $_POST["sendpoint"]);
					$_POST["mode"] = $key;
					$_POST[$key] = $value;
					$_POST["payment_charge"] = $_POST["money"];
					print_r($_POST);
				}
			}
		//}
	}
}
?>
