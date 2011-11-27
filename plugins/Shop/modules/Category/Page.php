<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");
LoadModel("CategoryModel", "Shopping");

/**
 * ### Shopping.Category.List
 * 商品カテゴリのリストを取得する。
 * @param item １ページあたりの件数
 * @param delta 現在ページの前後に表示するページ数
 * @param type 抽出するカテゴリのタイプ（指定しない場合は全タイプから抽出）
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Category_Page extends FrameworkModule{
	function execute($params){
		// ページャのオプションを設定
		$option = array();
		$option["mode"] = "Sliding";		// 現在ページにあわせて表示するページリストをシフトさせる。
		$option["perPage"] = $params->get("item", "10");	// １ページあたりの件数
		$option["delta"] = $params->get("delta", "3");		// 現在ページの前後に表示するページ番号の数（Slidingの場合は2n+1ページ分表示）
		$option["prevImg"] = "<";			// 前のページ用のテキスト
		$option["nextImg"] = ">";			// 次のページ用のテキスト
		$option["firstPageText"] = "<<"; 	// 最初のページ用のテキスト
		$option["lastPageText"] = ">>";		// 最後のページ用のテキスト
		$option["curPageSpanPre"] = "<font color=\"#000000\">";		// 現在ページのプレフィクス
		$option["curPageSpanPost"] = "</font>";		// 現在ページのサフィックス
		$option["clearIfVoid"] = false;			// １ページのみの場合のページリンクの出力の有無
		
		$category = new CategoryModel();
		$pager = $category->pager($option);
		$result = $pager->findAllBy(array("category_type_id" => $params->get("type")));
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "categories")] = $result;
	}
}
?>
