<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductCategoriesTable", "Shopping");
LoadTable("CategoriesTable", "Shopping");
LoadTable("CategoryTypesTable", "Shopping");

/**
 * ### Shopping.Product.Detail
 * 商品の詳細情報を取得する。
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Product_DetailCategory extends FrameworkModule{
	function execute($params){
		// この機能で使用するテーブルモデルを初期化
		$productCategories = new ProductCategoriesTable();
		$categories = new CategoriesTable();
		$categoryTypes = new CategoryTypesTable();

		// 商品のデータを取得する処理
		$select = new DatabaseSelect($categories);
		$select->addColumn($categoryTypes->category_type_id)->addColumn($categoryTypes->category_type)->addColumn($categories->_W);
		$select->joinInner($categoryTypes, array($categories->category_type_id." = ".$categoryTypes->category_type_id));
		$select->joinInner($productCategories, array($categories->category_id." = ".$productCategories->category_id));
		$select->addWhere($productCategories->product_id." = ?", array($_POST["product_id"]));
		$result = $select->execute();
		
		// 結果を返す。
		$cats = array();
		foreach($result as $item){
			$cats[$item["category_type_id"]] = $item;
		}
		$_SERVER["ATTRIBUTES"][$params->get("result", "categories")] = $cats;
	}
}
?>
