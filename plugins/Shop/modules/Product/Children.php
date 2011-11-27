<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductCategoriesTable", "Shopping");
LoadTable("ProductsTable", "Shopping");
LoadTable("CategoriesTable", "Shopping");
LoadTable("CategoryTypesTable", "Shopping");
LoadTable("ProductTypesTable", "Shopping");
LoadTable("ProductImagesTable", "Shopping");

/**
 * ### Shopping.Product.List
 * 商品のリストを取得する。
 * @param item １ページあたりの件数
 * @param delta 現在ページの前後に表示するページ数
 * @param category【カテゴリタイプ】 商品に紐付けするカテゴリ（条件にしない場合は空文字を設定）
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Product_Children extends FrameworkModule{
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
		
		// この機能で使用するテーブルモデルを初期化
		$products = new ProductsTable();
		$productImages = new ProductImagesTable();
		
		// パラメータを取得
		$shoppingType = $params->get("type", $_SERVER["CONFIGURE"]["SITE"]["shopping_type"]);

		// 新着のリストを取得する処理
		$select = new DatabaseSelect($products);
		$select->addColumn($products->_W);
		$select->addColumn($productImages->icon_image);
		$select->addColumn($productImages->list_image);
		$select->addColumn($productImages->detail_image);
		$select->addColumn($productImages->content_url);
		if(!empty($shoppingType)){
			$select->joinLeft($productImages, array($products->product_id." = ".$productImages->product_id, $productImages->type_id." = ?"), array($shoppingType));
		}else{
			$select->joinLeft($productImages, array($products->product_id." = ".$productImages->product_id));
		}
		$select->addWhere($products->parent_id." = ?", array($_POST["product_id"]));
		$select->addWhere($products->display_flg." = 1")->addWhere($products->delete_flg." = 0");
		$select->addGroupBy($products->product_id);
		$select->addOrder($products->sort_order, true);
		$select->addOrder("COALESCE(".$products->sale_start.", ".$products->create_time.")", true);
		$result = $select->executePager($option);
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "children")] = $result;
	}
}
?>
