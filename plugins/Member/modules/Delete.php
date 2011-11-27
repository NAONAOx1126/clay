<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerDeliverModel", "Members");
LoadModel("CustomerOptionModel", "Members");
LoadModel("CustomerTypeModel", "Members");

/**
 * 携帯の個体番号でのログインを実行するモジュールです。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_Delete extends FrameworkModule{
	function execute($params){
		if(isset($_POST["delete"]) && isset($_POST["customer_id"]) && !empty($_POST["customer_id"])){
			// トランザクションデータベースの取得
			$db = DBFactory::getLocal();// トランザクションの開始
			$db->beginTransaction();
			
			try{
				// 配送情報を削除
				$model = new CustomerDeliverModel();
				$delivers = $model->findAllByCustomer($_POST["customer_id"]);
				foreach($delivers as $deliver){
					$deliver->delete($db);
				}

				// オプション項目を削除
				$model = new CustomerOptionModel();
				$options = $model->findAllByCustomer($_POST["customer_id"]);
				foreach($options as $option){
					$option->delete($db);
				}
				
				// 顧客種別を削除
				$model = new CustomerTypeModel();
				$types = $model->findAllByCustomer($_POST["customer_id"]);
				foreach($types as $type){
					$type->delete($db);
				}
				
				// 顧客データを削除
				$customer = new CustomerModel();
				$customer->findByPrimaryKey($_POST["customer_id"]);
				$customer->delete($db);
				
				// エラーが無かった場合、処理をコミットする。
				$db->commit();
			}catch(Exception $ex){
				$db->rollBack();
				throw $ex;
			}
		}
	}
}
?>