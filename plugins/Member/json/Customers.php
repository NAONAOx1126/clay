<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("CustomerOptionModel", "Members");

// ページャのオプションを設定
$option = array();
$option["mode"] = "Sliding";		// 現在ページにあわせて表示するページリストをシフトさせる。
$option["perPage"] = (isset($_POST["perPage"])?$_POST["perPage"]:"80");	// １ページあたりの件数
$option["delta"] = "5";	// 現在ページの前後に表示するページ番号の数（Slidingの場合は2n+1ページ分表示）
$option["separator"] = "</li><li>";			// セパレータ
$option["prevImg"] = "<";			// 前のページ用のテキスト
$option["nextImg"] = ">";			// 次のページ用のテキスト
$option["firstPageText"] = ""; 	// 最初のページ用のテキスト
$option["lastPageText"] = "";		// 最後のページ用のテキスト
$option["curPageSpanPre"] = "<span>";		// 現在ページのプレフィクス
$option["curPageSpanPost"] = "</span>";		// 現在ページのサフィックス
$option["clearIfVoid"] = true;			// １ページのみの場合のページリンクの出力の有無

// カスタマモデルを使用して顧客情報を取得
$customer = new CustomerModel();
$customers = $customer->getCustomersArray($_POST, $option);

$result = $customers;
?>
