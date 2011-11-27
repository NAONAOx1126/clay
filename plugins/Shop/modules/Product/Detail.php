<?php
// ショッピングカートの設定を取得
LoadModel("Setting", "Shopping");
LoadModel("ProductModel", "Shopping");
LoadModel("ProductOptionModel", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductsTable", "Shopping");
LoadTable("ProductTypesTable", "Shopping");
LoadTable("ProductImagesTable", "Shopping");
LoadTable("ProductOptionsTable", "Shopping");


/**
 * ### Shopping.Product.Detail
 * 商品の詳細情報を取得する。
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Product_Detail extends FrameworkModule{
	function execute($params){
		// この機能で使用するテーブルモデルを初期化
		$products = new ProductsTable();
		$parents = new ProductsTable();
		$parents->setAlias("parents");
		$productTypes = new ProductTypesTable();
		$productImages = new ProductImagesTable();
		$productOptions = new ProductOptionsTable();

		// パラメータを取得
		$shoppingType = $params->get("type", $_SERVER["CONFIGURE"]["SITE"]["shopping_type"]);

		// 商品のデータを取得する処理
		$product = new ProductModel();
		if(!empty($_POST["parent_id"])){
			$product->findByPrimaryKey($_POST["parent_id"]);
		}else{
			$product->findByPrimaryKey($_POST["product_id"]);
		}

		// 検索結果をPOSTに渡す
		foreach($product->values as $key => $value){
			$_POST[$key] = $value;
		}
		if(empty($_POST["option1_id"])){ $_POST["option1_id"] = "0"; }
		if(empty($_POST["option2_id"])){ $_POST["option2_id"] = "0"; }
		if(empty($_POST["option3_id"])){ $_POST["option3_id"] = "0"; }
		if(empty($_POST["option4_id"])){ $_POST["option4_id"] = "0"; }
		$option = $product->option($_POST["option1_id"], $_POST["option2_id"], $_POST["option3_id"], $_POST["option4_id"]);
		foreach($option->values as $key => $value){
			$_POST[$key] = $value;
		}
		
		// 結果を返す。
		$_SERVER["ATTRIBUTES"][$params->get("result", "product")] = $product;
	}
}
?>
