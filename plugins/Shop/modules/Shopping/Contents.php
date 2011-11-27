<?php
// 共通処理を呼び出し。
LoadModel("Setting", "Shopping");
LoadModel("PrefModel");
LoadModel("MailTemplateModel");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("PaymentModel", "Shopping");
LoadModel("TempOrderModel", "Shopping");
LoadModel("TempOrderDetailModel", "Shopping");
LoadModel("OrderModel", "Shopping");
LoadModel("OrderDetailModel", "Shopping");
LoadModel("ProductModel", "Shopping");
LoadModel("ProductOptionModel", "Shopping");

class Shopping_Shopping_Contents extends FrameworkModule{
	function execute($params){
		// 除去した結果、もしくはカートの中が空でない場合は購入処理を続行。
		if(!empty($_SESSION[CART_SESSION_KEY])){
			// パラメータを取得する。
			$mode = $params->get("mode", "regist");
			
			// 購入完了処理を行う。(ダウンロードコンテンツ用）
			if(!empty($_POST[$mode])){
				// トランザクションデータベースの取得
				$db = DBFactory::getLocal();
				
				// トランザクションの開始
				$db->beginTransaction();
				
				try{
					// 仮受注テーブルにデータを設定する
					$order = new TempOrderModel($_SESSION[CUSTOMER_SESSION_KEY]);
					
					// 都道府県のIDを名前に変更する。
					$pref = new PrefModel();
					$pref->findByPrimaryKey($order->order_pref);
					$order->order_pref = $pref->name;
					$pref->findByPrimaryKey($order->deliv_pref);
					$order->deliv_pref = $pref->name;
	
					// 注文コードにユニークな値を設定する。
					$order->order_code = uniqid(date("YmdHis"));
					
					// ショップコメントを設定
					$order->shop_comment = "";
					
					// 注文ステータスを設定
					$order->order_status = "1";
					
					// テンプレートの設定により、PCかモバイルか識別し、注文タイプを設定
					if($_SERVER["USER_TEMPLATE"] == $_SERVER["CONFIGURE"]["SITE"]["mobile_template_name"]){
						$order->order_type = "モバイル";
					}else{
						$order->order_type = "PC";
					}
					
					// 決済方法の情報を取得する。
					$payment = new PaymentModel();
					$payment->findByPrimaryKey($order->payment_id);
					
					// 受注情報をDBに登録する。
					$order->save($db);
	
					foreach($_SESSION[CART_SESSION_KEY] as $cart){
						// 注文詳細データを登録
						$cart["order_id"] = $order->order_id;
						$cart["price"] = $cart["sale_price"];
						$orderDetail = new TempOrderDetailModel($cart);
						$orderDetail->save($db);
					}
					
					// エラーが無かった場合、処理をコミットする。
					$db->commit();
	
					// 支払い合計額が0円以上の場合は、決済を実行する。
					if($_SESSION[CUSTOMER_SESSION_KEY]->payment_total > 0){
						if($payment->credit_flg == "1"){
							// クレジットカードの場合はカードの画面に遷移する。
							throw new RedirectException(array("order_id" => $order->order_id));
						}else{
							$_POST["post_regist"] = "1";
							$_POST["order_id"] = $order->order_id;
						}
					}else{
						// 支払い合計金額が0円の場合は、そのまま決済完了とする。
						$_POST["post_regist"] = "1";
						$_POST["order_id"] = $order->order_id;
					}
				}catch(Exception $ex){
					unset($_POST["regist"]);
					$db->rollBack();
					throw $ex;
				}
			}
		}		
	}
}
?>
