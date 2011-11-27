<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("CustomerOptionModel", "Members");

/**
 * 携帯の個体番号でのログインを実行するモジュールです。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_CustomerCsv extends FrameworkModule{
	function execute($params){
		if(isset($_POST[$params->get("mode", "download")])){
			// カスタマモデルを使用して顧客情報を取得
			$customer = new CustomerModel();
			$customers = $customer->getCustomersArray($_SESSION["INPUT_DATA"]);
			
			$_SERVER["ATTRIBUTES"][$params->get("result", "customers")] = $customers;
		}
	}
}
?>