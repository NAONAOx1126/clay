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
 * <meta name="loadmodule" content="Shopping.Shopping.EpsillonComplete" [mode="add"] />
 * </samp>
 * - mode : このモジュールの処理を反応させるために必要なPOSTパラメータの名前
 *
 * @package Shopping
 * @author Naohisa Minagawa <info@sweetberry.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Shopping_Shopping_EpsillonComplete extends FrameworkModule{
	function execute($params){
		if(empty($_POST["order_id"]) && empty($_POST["order_number"]) && !empty($_POST["trans_code"])){
			// HTTP_Requestの初期化
			$request = new HTTP_Request($params->get("getsales2", "https://beta.epsilon.jp/cgi-bin/order/getsales2.cgi"), array("timeout" => "20"));
	
			//set method
			$request->setMethod(HTTP_REQUEST_METHOD_POST);
			//set post data
			$request->addPostData('contract_code', $params->get("code"));
			$request->addPostData('trans_code', $_POST["trans_code"]);
	
			// HTTPリクエスト実行
			$response = $request->sendRequest();
			
			if (!PEAR::isError($response)) {
				// 応答内容(XML)の解析
				$res_code = $request->getResponseCode();
				$res_header = $request->getResponseHeader();
				$res_content = $request->getResponseBody();
				switch($res_code){
					case "200":
						if(preg_match("/<result order_number=\"([0-9]+)\" \\/>/", $res_content, $params) > 0){
							$_POST["order_number"] = $params[1];
						}
						break;
				}
			}
		}
		if(!empty($_POST["order_number"])){
			if(strlen($_POST["order_number"]) > 11){
				$_POST["order_id"] = intval(substr($_POST["order_number"], 0, 11));
			}else{
				$_POST["order_id"] = intval($_POST["order_number"]);
			}
			$_POST["post_regist"] = "1";
		}
	}
}
?>
