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
class Shopping_Shopping_ZeroComplete extends FrameworkModule{
	function execute($params){
		// ゼロ決済からのアドレスの場合のみ処理する。
		if($_SERVER["REMOTE_ADDR"] == "210.164.6.67" || $_SERVER["REMOTE_ADDR"] == "202.221.139.50"){
			// 設定したクライアントIPが一致して決済結果がokの場合のみ処理する。
			if($params->get("client") == $_POST["clientip"] && $_POST["result"] == "ok"){
				// 仮受注データを取得
				$order = new TempOrderModel();
				$order->findByPrimaryKey($_POST["sendid"]);
				
				$_POST["post_regist"] = "1";
				$_POST["order_id"] = $order->order_id;
			}
		}
	}
}
?>
