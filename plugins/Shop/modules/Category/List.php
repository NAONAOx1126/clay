<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");
LoadModel("CategoryModel", "Shopping");

/**
 * ### Shopping.Category.List
 * 商品カテゴリのリストを取得する。
 * @param type 抽出するカテゴリのタイプ（指定しない場合は全タイプから抽出）
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Category_List extends FrameworkModule{
	function execute($params){
		$category = new CategoryModel();
		$result = $category->findAllByType($params->get("type"));
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "categories")] = $result;
	}
}
?>
