<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
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
class Members_Regist extends FrameworkModule{
	function execute($params){
		if(isset($_POST["regist"]) && !empty($_SESSION["INPUT_DATA"])){
			// トランザクションデータベースの取得
			$db = DBFactory::getLocal();// トランザクションの開始
			$db->beginTransaction();
			
			try{
				if($params->check("unique")){
					$check = new CustomerModel();
					$result = $check->findBy(array($params->get("unique")." = ".$_SESSION["INPUT_DATA"][$params->get("unique")]));
				}
				
				// 顧客データモデルを初期化
				$customer = new CustomerModel();
				$customer->findByPrimaryKey($_SESSION["INPUT_DATA"]["customer_id"]);
				
				// ユニーク設定が存在しないか、ユニークに該当するデータが無いか、ユニークデータと登録データのユーザーIDが一致する場合には登録処理を実行
				if(!$params->check("unique") || !$result || $check->customer_id == $_SESSION["INPUT_DATA"]["customer_id"]){
					if(empty($customer->customer_id)){
						 if(!empty($_SESSION["INPUT_DATA"]["customer_id"])){
							 $customer = new CustomerModel(array("customer_id" => $_SESSION["INPUT_DATA"]["customer_id"]));
						}else{
							$customer = new CustomerModel();
						}
					}
										
					// データを登録する。
					$customer->active_flg = "1";
					$customer->delete_flg = "0";
					foreach($_SESSION["INPUT_DATA"] as $name => $value){
						$customer->$name = $value;
					}
					$customer->save($db);
				}
				
				// エラーが無かった場合、処理をコミットする。
				$db->commit();
	
				// 結果を設定
				$_SERVER["ATTRIBUTES"][$params->get("result", "customer")]= $_SESSION[CUSTOMER_SESSION_KEY] = $customer;					
			}catch(Exception $ex){
				unset($_POST["regist"]);
				$db->rollBack();
				throw $ex;
			}
		}
	}
}
?>