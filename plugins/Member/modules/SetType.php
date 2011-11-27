<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("PointLogModel", "Members");
LoadModel("TypeModel", "Members");

/**
 * 携帯の個体番号でのログインを実行するモジュールです。
 *
 * @params session 顧客情報を保存するセッション名
 * @params type 設定する顧客種別
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_SetType extends FrameworkModule{
	function execute($params){
		// 設定するタイプのID
		$type_id = $params->get("type", $_POST["type"]);
		
		if(!empty($type_id)){
			// トランザクションデータベースの取得
			$db = DBFactory::getLocal();// トランザクションの開始
			$db->beginTransaction();
			
			try{
				// 現在のタイプが設定されているか調べる。
				if(empty($_SESSION[CUSTOMER_SESSION_KEY]->types[$type_id])){
					// 設定するタイプ情報を取得
					$type = new TypeModel();
					$type->findByPrimaryKey($type_id);
					
					// 決済金額と初期費用が一致するかチェック
					if(empty($type->init_price) || $_POST["payment_charge"] == $type->init_price){
						// 設定されていない場合はタイプ設定を追加
						$values = array("customer_id" => $_SESSION[CUSTOMER_SESSION_KEY]->customer_id, "type_id" => $type_id);
						$customerTypes = $_SESSION[CUSTOMER_SESSION_KEY]->types;
						$customerTypes[$type_id] = new CustomerTypeModel($values);
						$customerTypes[$type_id]->type_name = $type->type_name;
						$_SESSION[CUSTOMER_SESSION_KEY]->types = $customerTypes;
						
						// タイプ設定を追加した場合、ポイントを追加する。
						$_SESSION[CUSTOMER_SESSION_KEY]->point += $type->init_point;
						
						// 変更内容をデータベースに反映
						$_SESSION[CUSTOMER_SESSION_KEY]->save($db);
						$customerTypes = $_SESSION[CUSTOMER_SESSION_KEY]->types;
						foreach($customerTypes as $customerType){
							$customerType->save($db);
						}

						// ポイントログに書き込み
						$pointLog = new PointLogModel();
						$pointLog->save($db, $type->init_point);
					}
				}
				
				// エラーが無かった場合、処理をコミットする。
				$db->commit();
					
			}catch(Exception $ex){
				$db->rollBack();
				throw $ex;
			}
		}
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "customer")] = $_SESSION[CUSTOMER_SESSION_KEY];
	}
}
?>