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
class Members_CustomerList extends FrameworkModule{
	function execute($params){
		// ページャのオプションを設定
		$option = array();
		$option["mode"] = "Sliding";		// 現在ページにあわせて表示するページリストをシフトさせる。
		$option["perPage"] = $params->get("item", "100");	// １ページあたりの件数
		$option["delta"] = $params->get("delta", "5");		// 現在ページの前後に表示するページ番号の数（Slidingの場合は2n+1ページ分表示）
		$option["separator"] = $params->get("separator", "</li><li>");			// セパレータ
		$option["prevImg"] = "<";			// 前のページ用のテキスト
		$option["nextImg"] = ">";			// 次のページ用のテキスト
		$option["firstPageText"] = "<<"; 	// 最初のページ用のテキスト
		$option["lastPageText"] = ">>";		// 最後のページ用のテキスト
		$option["curPageSpanPre"] = "<span>";		// 現在ページのプレフィクス
		$option["curPageSpanPost"] = "</span>";		// 現在ページのサフィックス
		$option["clearIfVoid"] = true;			// １ページのみの場合のページリンクの出力の有無
		
		// カスタマモデルを使用して顧客情報を取得
		$customer = new CustomerModel();

		if($params->check("order")){
			$_SESSION["INPUT_DATA"]["order:".$params->get("order")] = 0;
		}
		if($params->check("rorder")){
			$_SESSION["INPUT_DATA"]["order:".$params->get("rorder")] = 1;
		}
		if($params->check("order_num")){
			$_SESSION["INPUT_DATA"]["order_num:".$params->get("order_num")] = 0;
		}
		if($params->check("rorder_num")){
			$_SESSION["INPUT_DATA"]["order_num:".$params->get("rorder_num")] = 1;
		}
		if($params->check("positive")){
			$_SESSION["INPUT_DATA"][">=:".$params->get("positive")] = 1;
		}
		$customers = $customer->getCustomersArray($_SESSION["INPUT_DATA"], $option);
		
		if($params->check("order")){
			unset($_SESSION["INPUT_DATA"]["order:".$params->get("order")]);			
		}
		if($params->check("rorder")){
			unset($_SESSION["INPUT_DATA"]["order:".$params->get("rorder")]);
		}
		if($params->check("order_num")){
			unset($_SESSION["INPUT_DATA"]["order_num:".$params->get("order_num")]);			
		}
		if($params->check("rorder_num")){
			unset($_SESSION["INPUT_DATA"]["order_num:".$params->get("rorder_num")]);			
		}
		if($params->check("positive")){
			unset($_SESSION["INPUT_DATA"][">:".$params->get("positive")]);
		}

		$_SERVER["ATTRIBUTES"][$params->get("result", "customers")] = $customers;
	}
}
?>