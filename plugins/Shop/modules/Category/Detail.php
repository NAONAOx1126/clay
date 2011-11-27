<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");
LoadModel("CategoryModel", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("SiteCategoriesTable", "Shopping");
LoadTable("CategoriesTable", "Shopping");
LoadTable("CategoryTypesTable", "Shopping");
LoadTable("ProductCategoriesTable", "Shopping");

/**
 * ### Shopping.Category.Detail
 * 商品カテゴリの詳細情報を取得する。
 * @param type 抽出するカテゴリのタイプ（指定しない場合は全タイプから抽出）
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Category_Detail extends FrameworkModule{
	function execute($params){
		// 登録されているカテゴリタイプのリストを取得
		$category = new CategoryModel();
		$category->findByPrimaryKey($_POST["category".$params->get("type")]);
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "category")] = $category;
	}
}
?>
