<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");
LoadModel("ProductModel", "Shopping");
LoadModel("ProductCategoryModel", "Shopping");

/**
 * ### Shopping.Product.New
 * 新規入荷商品のリストを取得する。
 * @param item １ページあたりの件数
 * @param delta 現在ページの前後に表示するページ数
 * @param category【カテゴリタイプ】 商品に紐付けするカテゴリ（条件にしない場合は空文字を設定）
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Product_New extends FrameworkModule{
	function execute($params){
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
		
		$product = new ProductModel();
		$productCategory = new ProductCategoryModel();
		$pager = $product->pager($option);
		if($params->check("category")){
			$result = $pager->findAllByWith($productCategory, array("product_id" => "product_id"), "category_id", $params->get("category"), array(), $product->access->create_time);
		}else{
			$result = $pager->findAllBy(array(), $product->access->create_time);
		}
				
		$_SERVER["ATTRIBUTES"][$params->get("result", "products")] = $result;
	}
}
?>
