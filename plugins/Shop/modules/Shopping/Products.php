<?php
// 共通処理を呼び出し。
LoadModel("Searches", "Shopping");
LoadModel("Checks", "Shopping");
LoadModel("Registers", "Shopping");
LoadModel("Mails", "Shopping");

class Shopping_Shopping_Purchase extends FrameworkModule{
	function execute(){
		// 購入完了処理
		if(!empty($_POST["regist"])){
			// 入力内容に不備が無いか最終確認
			$errors = array();
			$errors = Checks::Personal("order", "注文者", $_SESSION["customer"], $errors);
			$errors = Checks::Email("order", "注文者", $_SESSION["customer"], $errors);
			$errors = Checks::Personal("deliv", "配送先", $_SESSION["customer"], $errors);
			$errors = Checks::Payment($_SESSION["customer"], $errors);
			// エラーがあった場合、入力エラー例外をスロー
			if(!empty($errors)){
				unset($_POST["regist"]);
				throw new InvalidException($errors);
			}
		
			// トランザクションデータベースの取得
			$db = DBFactory::getLocal();
			
			// トランザクションの開始
			$db->beginTransaction();
			
			try{
				// セッションから、受注用の情報を構築する。
				$keys = array("customer_id", "order_sei", "order_mei", "order_sei_kana", "order_mei_kana", "order_email", "order_zip1", "order_zip2", 
										 "order_pref", "order_address1", "order_address2", "order_tel1", "order_tel2", "order_tel3", "order_fax1", "order_fax2", "order_fax3", 
										 "deliv_sei", "deliv_mei", "deliv_sei_kana", "deliv_mei_kana", "deliv_zip1", "deliv_zip2", "deliv_pref", "deliv_address1", "deliv_address2", 
										 "deliv_tel1", "deliv_tel2", "deliv_tel3", "customer_comment", "payment_id", "delivery_id", "delivery_date", "delivery_time", 
										 "subtotal", "adjust_title", "adjust", "discount_title", "discount", "charge", "deliv_fee", "total", "point", "payment_total");
				$order = array();
				foreach($keys as $key){
					$order[$key] = $_SESSION["customer"][$key];
				}
				$order["order_pref"] = $_SERVER["ATTRIBUTES"]["prefs"][$order["order_pref"]];
				$order["deliv_pref"] = $_SERVER["ATTRIBUTES"]["prefs"][$order["deliv_pref"]];
				$order["order_code"] = sprintf("%011d", $order["order_tel1"].$order["order_tel2"].$order["order_tel3"]).date("YmdHis").sprintf("%05d", rand(1, 99999));
				$order["shop_comment"] = "";
				$order["order_status"] = "1";
				if($_SERVER["USER_TEMPLATE"] == $_SERVER["CONFIGURE"]["SITE"]["mobile_template_name"]){
					$order["order_type"] = "モバイル";
				}else{
					$order["order_type"] = "PC";
				}
				
				if(!empty($_SESSION["cart"])){
					// 受注情報をDBに登録する。
					$order = Registers::TempOrder($db, $order);

					// セッションから受注詳細用のデータを構築する。
					$details = array();
					$stockKeys = array();
					foreach($_SESSION["cart"] as $cart){
						$detail = array("order_id" => $order["order_id"]);
						$detail["product_id"] = $cart["product_id"];
						$detail["product_code"] = $cart["product_code"];
						$detail["product_name"] = $cart["product_name"];
						$stockKeys[] = "product_id";
						for($i =1; $i <= 9; $i ++){
							$detail["option".$i."_id"] = $cart["option".$i."_id"];
							$detail["option".$i."_code"] = $cart["option".$i."_code"];
							$detail["option".$i."_name"] = $cart["option".$i."_name"];
							$stockKeys[] = "option".$i."_id";
						}
						$detail["price"] = $cart["sale_price"];
						$detail["quantity"] = $cart["quantity"];
						$detail["point_rate"] = "0";
						$details[] = $detail;
					}
					
					// 受注詳細情報をDBに登録する。
					Registers::TempOrderDetails($db, $details, $stockKeys, "quantity");
		
					// エラーが無かった場合、処理をコミットする。
					$db->commit();
					
					// 注文IDをクッキーに保存する。（決済完了せず、前の画面に戻った場合は在庫を戻す）
					setcookie("inprocess_order_id", $order["order_id"], time() + 365 * 24 * 3600);

					// クレジットカードか判断してカードの場合はカード画面に遷移
					if($_SERVER["ATTRIBUTES"]["credits"][$order["payment_id"]] == "1"){
						// リダイレクト用例外を発行
						throw new RedirectException(array("order_id" => $order["order_id"]));
					}else{
						$_POST["post_regist"] = "1";
						$_POST["order_id"] = $order["order_id"];
					}
				}else{
					throw new InvalidException(array("カートの中身がありません"));
				}
			}catch(Exception $ex){
				unset($_POST["regist"]);
				$db->rollBack();
				throw $ex;
			}
		}
		
		// 購入完了後処理
		if(!empty($_POST["post_regist"])){
			// データベースの初期化	
			$db = DBFactory::getLocal();// トランザクションの開始
			$db->beginTransaction();
		
			try{
				// 受注完了メール用に受注データを取得する。
				$order = Searches::TempOrder($_POST["order_id"], $db);
				
				if(!empty($order) && is_array($order["details"])){
					// 受注データを仮テーブルから本テーブルに移動する。
					Registers::CommitTemps($db, $_POST["order_id"]);
					
					// 受注完了メール送信
					Mails::Order($db, $_SERVER["CONFIGURE"]["SITE"]["order_mail_subject"], $_SERVER["CONFIGURE"]["SITE"]["order_mail_header"], $_SERVER["CONFIGURE"]["SITE"]["order_mail_footer"], $order, $_SERVER["CONFIGURE"]["SITE"]);
							
					// エラーが無かった場合、次のページへ
					$db->commit();
					
					// 確定したら、カートの中身をクリアする。
					unset($_SESSION["cart"]);
			
					// 購入完了したので、クッキーを削除
					setcookie("inprocess_order_id", "0", -1);
	
					// 注文データを結果として返す
					$_SERVER["ATTRIBUTES"]["order"] = $order;
				}else{
					throw new InvalidException(array("カートの中身がありません"));
				}
			}catch(Exception $ex){
				$db->rollBack();
				throw $ex;
			}
		}
	}
}
?>
