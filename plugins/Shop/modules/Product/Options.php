<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductCategoriesTable", "Shopping");
LoadTable("ProductsTable", "Shopping");
LoadTable("ProductOptionsTable", "Shopping");
LoadTable("OptionsTable", "Shopping");
LoadTable("OptionTypesTable", "Shopping");

/**
 * ### Shopping.Product.Options
 * 商品オプションのリストを取得する。
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Product_Options extends FrameworkModule{
	function execute($params){
		// この機能で使用するテーブルモデルを初期化
		$productCategories = new ProductCategoriesTable();
		$productOptions = new ProductOptionsTable();

		// テーブルにエイリアスを設定
		$optOptions = array();
		$optOptionTypes = array();
		for($i = 1; $i <= 9; $i ++){
			$optOptions[$i] = new OptionsTable();
			$optOptions[$i]->setAlias("options_".$i);
			$optOptionTypes[$i] = new OptionTypesTable();
			$optOptionTypes[$i]->setAlias("option_types_".$i);
		}

		// 新着のリストを取得する処理
		$select = new DatabaseSelect($productOptions);
		$select->addColumn($productOptions->_W);
		for($i = 1; $i <= 9; $i ++){
			$option_name = "option".$i."_id";
			$select->addColumn($optOptions[$i]->option_code, "option".$i."_code");
			$select->addColumn($optOptions[$i]->option_name, "option".$i."_name");
			$select->addColumn($optOptionTypes[$i]->option_type_name, "option".$i."_type_name");
			$select->joinLeft($optOptions[$i], array($productOptions->$option_name." = ".$optOptions[$i]->option_id));
			$select->joinLeft($optOptionTypes[$i], array($optOptions[$i]->option_type_id." = ".$optOptionTypes[$i]));
			$select->addOrder($optOptions[$i]->sort_order);
		}
		
		$select->addWhere($productOptions->product_id." = ?", array($_POST["product_id"]));
		$select->addWhere($products->display_flg." = 1")->addWhere($products->delete_flg." = 0");
		$result = $select->execute();
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "options")] = $result[0];
	}
}
?>
