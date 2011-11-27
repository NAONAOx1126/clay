<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductCategoriesTable", "Shopping");
LoadTable("ProductsTable", "Shopping");

/**
 * ### Shopping.Product.Detail
 * 商品の詳細情報を取得する。
 * @param result 結果を設定する配列のキーワード
 */
class Shopping_Product_Check extends FrameworkModule{
	function execute($params){
		// パラメータを取得する。
		$mode = $param->get("mode", "check");
		
		if(!empty($_POST[$mode])){
			// この機能で使用するテーブルモデルを初期化
			$productCategories = new ProductCategoriesTable();
			$products = new ProductsTable();
			$productOptions = new ProductOptionsTable();
	
			// 新着のリストを取得する処理
			$select = new DatabaseSelect($products);
			$select->addColumn($products->_W);
			$select->joinInner($productOptions, array($products->product_id." = ".$productOptions->product_id));
			$select->addGroupBy($products->product_id);
			$select->addOrder($products->sort_order, true)->addOrder($products->create_date, true);
			$select->addWhere($products->product_id." = ?", array($_POST["product_id"]));
			for($i = 1; $i <= 9; $i ++){
				$name = "option".$i."_id";
				if(!empty($_POST[$name])){
					$select->addWhere($products->$name." = ?", array($_POST[$name]));
				}else{
					$select->addWhere($products->$name." IS NULL");
				}
			}
			$select->addWhere($products->display_flg." = 1")->addWhere($products->delete_flg." = 0");
			$result = $select->execute();
			
			if(count($result) == 0){
				unset($_POST[$mode]);
				throw new InvalidException(array("該当の商品はありません。"));
			}else{
				if(is_array($result[0])){
					foreach($result[0] as $key => $value){
						$_POST[$key] = $value;
					}
				}
			}
		}
	}
}
?>
