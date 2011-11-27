<?php
/**
 * ### Shop.Order.List
 * 商品のリストを取得する。
 * @param item １ページあたりの件数
 * @param delta 現在ページの前後に表示するページ数
 * @param category【カテゴリタイプ】 商品に紐付けするカテゴリ（条件にしない場合は空文字を設定）
 * @param result 結果を設定する配列のキーワード
 */
class Shop_Order_List extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		// ページャのオプションを設定
		$option = array();
		$option["mode"] = "Sliding";		// 現在ページにあわせて表示するページリストをシフトさせる。
		$option["perPage"] = $params->get("item", "10");			// １ページあたりの件数
		$option["delta"] = $params->get("delta", "3");				// 現在ページの前後に表示するページ番号の数（Slidingの場合は2n+1ページ分表示）
		$option["prevImg"] = "<";			// 前のページ用のテキスト
		$option["nextImg"] = ">";			// 次のページ用のテキスト
		$option["prevAccessKey"] = "*";			// 前のページ用のアクセスキー
		$option["nextAccessKey"] = "#";			// 次のページ用のアクセスキー
		$option["firstPageText"] = "<<"; 	// 最初のページ用のテキスト
		$option["lastPageText"] = ">>";		// 最後のページ用のテキスト
		$option["curPageSpanPre"] = "<font color=\"#000000\">";		// 現在ページのプレフィクス
		$option["curPageSpanPost"] = "</font>";		// 現在ページのサフィックス
		$option["clearIfVoid"] = false;			// １ページのみの場合のページリンクの出力の有無
		$option["fileName"] = $_SERVER["TEMPLATE_NAME"];
		$option["fixFileName"] = false;
		
		$order = $loader->loadModel("OrderModel");
		
		// パラメータのsortを並び順変更のキーとして利用
		$sortKey = $_POST[$params->get("order", "order")];
		unset($_POST[$params->get("order", "order")]);
		$conditions = array();
		foreach($_POST as $key => $value){
			if(!empty($value)){
				$conditions[$key] = $value;
			}
		}
		
		// 取得する件数の上限をページャのオプションに追加
		$option["totalItems"] = $order->countBy($conditions);
		
		// ページャーのインスタンスを作成
		$pager = AdvancedPager::factory($option);
		
		// 最終的に出力するページオブジェクトを生成
		$page = array();
		// ページャーからリンクをコピー
		$page["links"] = $pager->links;
		$page["links_object"] = $pager->getLinks();

		// オプションから該当件数を取得
		$page["totalItems"] = $options["totalItems"];

		// ページ数をページャーから取得
		$page["page_numbers"] = array(
			"current" => $pager->getCurrentPageID(),
			"total"   => $pager->numPages()
		);

		// 現在のページにおけるデータ全体に対するインデックスを取得
		list($page["from"], $page["to"]) = $pager->getOffsetByPageId();

		// 現在のページの実件数を取得
		$page["limit"] = $page["to"] - $page["from"] +1;
		
		// 取得する件数を絞り込み
		$order->limit($options["perPage"], $page["from"]-1);
		$page["data"] = $order->findAllBy($conditions, $sortKey);

		$_SERVER["ATTRIBUTES"][$params->get("result", "orders")] = $page;
	}
}
?>
