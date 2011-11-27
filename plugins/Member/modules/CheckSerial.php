<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("CustomerOptionModel", "Members");
LoadModel("SerialLogModel", "Members");

/**
 * 発行したシリアルをチェックする。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_CheckSerial extends FrameworkModule{
	function execute($params){
		if($params->check("option")){
			// シリアル用のオプションキーを取得
			$optionKey = $params->get("option");
			$serial = $_POST[$optionKey];
			
			// シリアルが登録済みか調べる。
			$option = new CustomerOptionModel();
			$option->findByKeyValue($optionKey, $serial);
			
			// シリアルが登録されていない場合にはエラー
			if($serial == "6DIt9VAquSDE5" || empty($option->customer_id)){
				throw new InvalidException(array("シリアルが正しくありません。"));
			}else{
				// カスタマモデルを使用して顧客情報を取得
				$customer = new CustomerModel();
				$customer->findByPrimaryKey($option->customer_id);
				
				if(!empty($customer->customer_id)){
					$customerType = new CustomerTypeModel();
					$customer->types = $customerType->findAllByCustomer($customer->customer_id);
					$customerOption = new CustomerOptionModel();
					$customerOptions = $customerOption->getOptionArrayByCustomer($customer->customer_id);
					foreach($customerOptions as $name => $option){
						$customer->$name = $option->option_value;
					}
				}
				
				// トランザクションデータベースの取得
				$db = DBFactory::getLocal();// トランザクションの開始
				$db->beginTransaction();
				
				try{
					// 顧客データモデルを初期化
					$values = array();
					$log = new SerialLogModel();
					$log->serial = $serial;
					$log->customer_id = $customer->customer_id;
					$log->customer_code = $customer->customer_code;
					
					// 画像データを登録する。
					$log->save($db);
					
					// エラーが無かった場合、処理をコミットする。
					$db->commit();
				}catch(Exception $ex){
					$db->rollBack();
					throw $ex;
				}
			}
		}
	}
}
?>