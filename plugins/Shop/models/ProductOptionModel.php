<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductOptionsTable", "Shopping");

LoadModel("ProductModel", "Shopping");

/**
 * 顧客情報のモデルクラス
 */
class ProductOptionModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new ProductOptionsTable(), $values);
	}
	
	function findByPrimaryKey($product_id, $option1_id = 0, $option2_id = 0, $option3_id = 0, $option4_id = 0){
		$condition = array();
		$condition["product_id"] = $product_id;
		$condition["option1_id"] = $option1_id;
		$condition["option2_id"] = $option2_id;
		$condition["option3_id"] = $option3_id;
		$condition["option4_id"] = $option4_id;
		$this->findBy($condition);
	}
	
	function findAllByProduct($product_id){
		return $this->findAllBy(array("product_id" => $product_id));
	}
	
	function product(){
		$product = new ProductModel();
		$product->findByPrimaryKey($this->product_id);
		return $product;
	}
}
?>