<?php
// 共通処理を呼び出し。
LoadModel("Setting", "Shopping");
LoadModel("PrefModel");
LoadModel("MailTemplateModel");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("PointLogModel", "Members");
LoadModel("PaymentModel", "Shopping");
LoadModel("TempOrderModel", "Shopping");
LoadModel("TempOrderDetailModel", "Shopping");
LoadModel("OrderModel", "Shopping");
LoadModel("OrderDetailModel", "Shopping");
LoadModel("ProductModel", "Shopping");
LoadModel("ProductOptionModel", "Shopping");

class Shopping_Shopping_Complete extends FrameworkModule{
	function execute($params){
		// 除去した結果、もしくはカートの中が空でない場合は購入処理を続行。
		if(!empty($_SESSION[CART_SESSION_KEY])){
			// 購入完了後処理
			if(!empty($_POST["post_regist"])){
				// データベースの初期化	
				$db = DBFactory::getLocal();// トランザクションの開始
				$db->beginTransaction();
			
				try{
					// 受注完了メール用に受注データを取得する。
					$order = new TempOrderModel();
					$order->findByPrimaryKey($_POST["order_id"]);
					$orderDetails = TempOrderDetailModel::getOrderDetails($_POST["order_id"]);
					
					if(!empty($order->order_id) && is_array($orderDetails)){
						// 受注データをコピー
						$newOrder = new OrderModel($order);
						$newOrder->save($db);
						
						// 受注明細データをコピー
						$newOrder->details = array();
						foreach($orderDetails as $detail){
							$newDetail = new OrderDetailModel($detail);
							$newDetail->save($db);
							$newOrder->details[] = $newDetail;
							
							// 該当する在庫データを引き当て
							$productOption = new ProductOptionModel();
							$productOption->findByPrimaryKey($newDetail->product_id, $newDetail->option1_id, $newDetail->option2_id, $newDetail->option3_id, $newDetail->option4_id, $newDetail->option5_id, $newDetail->option6_id, $newDetail->option7_id, $newDetail->option8_id, $newDetail->option9_id);
							if($productOption->stock_unlimited != "1"){
								if($productOption->stock > $newDetail->quantity){
									$productOption->stock -= $newDetail->quantity;
									$productOption->save($db);
								}else{
									throw new InvalidException(array("購入商品の在庫が足りません"));
								}
							}
						}
						
						// ポイント利用を設定した場合には、ポイントを減算する。指定したポイントに足りない場合はエラー
						if($_SESSION[CUSTOMER_SESSION_KEY]->use_point < $_SESSION[CUSTOMER_SESSION_KEY]->point){
							// ポイント払いで必要なポイントがある場合は、ポイントを減算して購入完了に進める。
							$_SESSION[CUSTOMER_SESSION_KEY]->point -= $_SESSION[CUSTOMER_SESSION_KEY]->use_point;
							$_SESSION[CUSTOMER_SESSION_KEY]->save($db);
							
							// ポイントログに登録
							$pointLog = new PointLogModel();
							$pointLog->save($db, - $_SESSION[CUSTOMER_SESSION_KEY]->use_point);
						}else{
							throw new InvalidException(array("指定された利用ポイントが所持ポイントより不足しています。"));
						}

						// エラーが無かった場合、次のページへ
						$db->commit();
						
						// 確定したら、カートの中身をクリアする。
						unset($_SESSION["cart"]);
		
						// 注文データを結果として返す
						$_SERVER["ATTRIBUTES"][$params->get("result", "order")] = $newOrder;
					}else{
						throw new InvalidException(array("既に注文完了しております。"));
					}
				}catch(Exception $ex){
					$db->rollBack();
					throw $ex;
				}
			}
		}else{
			throw new InvalidException(array("カートの中身がありません"));
		}		
	}
}
?>
