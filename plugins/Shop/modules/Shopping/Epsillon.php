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

// この機能で使用するモデルクラス
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
 * <meta name="loadmodule" content="Shopping.Shopping.Epsillon" [mode="add"] />
 * </samp>
 * - mode : このモジュールの処理を反応させるために必要なPOSTパラメータの名前
 *
 * @package Shopping
 * @author Naohisa Minagawa <info@sweetberry.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Shopping_Shopping_Epsillon extends FrameworkModule{
	function execute($params){
		if(empty($_POST["order_id"]) && empty($_POST["order_number"]) && !empty($_POST["trans_code"])){
			// 仮受注データを取得
			$order = new TempOrderModel();
			$order->findByPrimaryKey($_POST["order_id"]);			
			
			// 仮受注データがある場合のみ、クレジット決済に進む
			if(!empty($order->order_id)){
				// HTTP_Requestの初期化
				$request = new HTTP_Request($params->get("receive_order3", "https://beta.epsilon.jp/cgi-bin/order/receive_order3.cgi"), array("timeout" => "20"));
		
				//set method
				$request->setMethod(HTTP_REQUEST_METHOD_POST);
				//set post data
				$request->addPostData('contract_code', $params->get("code"));
				$request->addPostData('user_id', $order->customer_id);
				$request->addPostData('user_name', mb_convert_encoding($order->order_sei.$order->order_mei, "EUC-JP", "UTF-8"));
				$request->addPostData('user_mail_add', $order->order_email);
				$request->addPostData('item_code', $params->get("item_code", "item"));
				$request->addPostData('item_name', mb_convert_encoding($params->get("item_name", "商品"), "EUC-JP", "UTF-8"));
				$request->addPostData('order_number', $order->order_id);
				$request->addPostData('st_code', $_SERVER["CONFIGURE"]["SITE"]["st_code_".$order->payment_id]);
				$request->addPostData('mission_code', "1");
				$request->addPostData('item_price', $order->payment_total);
				$request->addPostData('process_code', "1");
				$request->addPostData('xml', "0");
	
				// HTTPリクエスト実行
				$response = $request->sendRequest();
			
				if (!PEAR::isError($response)) {
					// 応答内容(XML)の解析
					$res_code = $request->getResponseCode();
					$res_header = $request->getResponseHeader();
					$res_content = $request->getResponseBody();
					switch($res_code){
						case "200":
							header("HTTP/1.0 200 Success");
							break;
						case "302":
							header("HTTP/1.0 302 Found");
							break;
						default:
							header("HTTP/1.0 404 Not Found");
							break;
					}
					foreach($res_header as $name => $value){
						if($name == "transfer-encoding" && $value == "chunked"){
							continue;
						}
						header($name.": ".$value);
					}
					$url = $_SERVER["CONFIGURE"]["SITE"]["epsillon_order_url"];
					
					$res_content = str_replace("/images/", substr($url, 0, strpos($url, "/", strpos($url, "://") + 3))."/images/", $res_content);
					echo $res_content;
				}
			}
		}
	}
}
?>
