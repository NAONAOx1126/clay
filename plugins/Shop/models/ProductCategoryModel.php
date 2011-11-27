<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductCategoriesTable", "Shopping");

LoadModel("ProductModel", "Shopping");
LoadModel("CategoryModel", "Shopping");

/**
 * カテゴリ情報のモデルクラス
 */
class ProductCategoryModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new ProductCategoriesTable(), $values);
	}
	
	function findByPrimaryKey($product_id, $category_id){
		$this->findBy(array("product_id" => $product_id, "category_id" => $category_id));
	}
	
	function findAllByProduct($product_id){
		return $this->findAllBy(array("product_id" => $product_id));
	}
	
	function findAllByCategory($category_id){
		return $this->findAllBy(array("category_id" => $category_id));
	}
	
	function product(){
		$product = new ProductModel();
		$product->findByPrimaryKey($this->product_id);
		return $product;
	}

	function category(){
		$category = new CategoryModel();
		$category->findByPrimaryKey($this->category_id);
		return $category;
	}
}
?>