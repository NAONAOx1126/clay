<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductImagesTable", "Shopping");

LoadModel("ProductModel", "Shopping");

/**
 * カテゴリ情報のモデルクラス
 */
class ProductImageModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new ProductImagesTable(), $values);
	}
	
	function findByPrimaryKey($product_id, $type_id){
		$this->findBy(array("product_id" => $product_id, "type_id" => $type_id));
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