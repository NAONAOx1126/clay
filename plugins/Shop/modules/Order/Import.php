<?php
/**
 * ### Shop.Order.Import
 * 注文情報をインポートするためのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Shop
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param key インポートするファイルの形式を特定するためのキー
 */
class Shop_Order_Import extends FrameworkModule{
	function execute($params){
		if($params->check("key") && is_array($_SERVER["ATTRIBUTES"][$params->get("key")])){
			foreach($_SERVER["ATTRIBUTES"][$params->get("key")] as $data){
				try{
					// トランザクションデータベースの取得
					$db = DBFactory::getConnection();
					
					// トランザクションの開始
					$db->beginTransaction();
					
					// ローダーを初期化
					$loader = new PluginLoader("Shop");
					
					// 配送方法のテーブルが存在しなければ追加
					if(empty($data["delivery_id"]) && !empty($data["delivery_name"])){
						$delivery = $loader->loadModel("DeliveryModel");
						$delivery->findBy(array("delivery_name" => $data["delivery_name"]));
						if($delivery->delivery_id == ""){
							$delivery->delivery_name = $data["delivery_name"];
							$delivery->deliv_fee = $data["deliv_fee"];
							$delivery->sort_order = "0";
							$delivery->save($db);
						}
						$data["delivery_id"] = $delivery->delivery_id;
					}
					
					// 決済方法のテーブルが存在しなければ追加
					if(empty($data["payment_id"]) && !empty($data["payment_name"])){
						$payment = $loader->loadModel("PaymentModel");
						$payment->findBy(array("payment_name" => $data["payment_name"]));
						if($payment->payment_id == ""){
							$payment->payment_name = $data["payment_name"];
							$payment->charge = $data["charge"];
							$payment->credit_flg = "0";
							$payment->sort_order = "0";
							$payment->save($db);
						}
						$data["payment_id"] = $payment->payment_id;
					}
					
					// 注文データが存在しなければ追加
					if(!empty($data["order_code"])){
						$order = $loader->loadModel("OrderModel");
						$order->findByCode($data["order_code"]);
						if($order->order_id == ""){
							foreach($data as $key => $value){
								$order->$key = $value;
							}
							$order->save($db);
						}
						$data["order_id"] = $order->order_id;
					}
					
					if(!empty($data["order_id"])){
						// 注文決済データが存在しない場合には追加
						$orderPayment = $loader->loadModel("OrderPaymentModel");
						$orderPayments = $orderPayment->findAllByOrder($data["order_id"]);
						if(empty($orderPayments)){
							foreach($data as $key => $value){
								$orderPayment->$key = $value;
							}
							$orderPayment->save($db);
						}
						$data["order_payment_id"] = $orderPayment->order_payment_id;

						// 注文セットデータが存在しなければ追加
						$orderPackage = $loader->loadModel("OrderPackageModel");
						$orderPackage->findBy(array("order_id" => $data["order_id"]));
						if($orderPackage->order_package_id == ""){
							foreach($data as $key => $value){
								$orderPackage->$key = $value;
							}
							$orderPackage->save($db);
						}
						$data["order_package_id"] = $orderPackage->order_package_id;

						if(!empty($data["order_package_id"])){
							// 注文セットデータが存在しなければ追加
							$orderDetail = $loader->loadModel("OrderDetailModel");
							$orderDetail->findBy(array("order_package_id" => $data["order_package_id"], "product_code" => $data["product_code"]));
							if($orderDetail->order_detail_id == ""){
								foreach($data as $key => $value){
									$orderDetail->$key = $value;
								}
								$orderDetail->save($db);
							}
							$data["order_detail_id"] = $orderDetail->order_detail_id;
						}
					}
					
					$db->commit();
				}catch(Exception $e){
					$db->rollback();
				}
			}
		}
	}
}
?>