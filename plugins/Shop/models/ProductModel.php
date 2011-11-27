<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("ProductsTable", "Shopping");
LoadTable("ProductCategoriesTable", "Shopping");
LoadTable("CategoriesTable", "Shopping");
LoadTable("CategoryTypesTable", "Shopping");
LoadTable("ProductTypesTable", "Shopping");
LoadTable("ProductImagesTable", "Shopping");
LoadTable("OrderDetailsTable", "Shopping");

LoadModel("ProductCategoryModel", "Shopping");
LoadModel("ProductImageModel", "Shopping");

/**
 * 顧客情報のモデルクラス
 */
class ProductModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new ProductsTable(), $values);
	}
	
	function findByPrimaryKey($product_id){
		$this->findBy(array("product_id" => $product_id));
	}
	
	function findAllByParent($parent_id){
		$model = new ProductModel();
		return $model->findAllBy(array("parent_id" => $parent_id));
	}
	
	function parent(){
		$product = new ProductModel();
		$product->findByPrimaryKey($this->parent_id);
		return $product;
	}

	function children(){
		$model = new ProductModel();
		return $model->findAllByParent($this->product_id);
	}
	
	function categories(){
		$model = new ProductCategoryModel();
		return $model->findAllByProduct($this->product_id);
	}

	function category($type_id){
		$categories = $this->categories();
		foreach($categories as $category){
			if($category->category()->category_type_id == $type_id){
				return $category;
			}
		}
		return new ProductCategoryModel();
	}

	function images(){
		$model = new ProductImageModel();
		return $model->findAllByProduct($this->product_id);
	}

	function image($type_id){
		$image = new ProductImageModel();
		$image->findByPrimaryKey($this->product_id, $type_id);
		return $image;
	}

	function options(){
		$model = new ProductOptionModel();
		return $model->findAllByProduct($this->product_id);
	}

	function option($option1_id = 0, $option2_id = 0, $option3_id = 0, $option4_id = 0){
		$option = new ProductOptionModel();
		$option->findByPrimaryKey($this->product_id, $option1_id, $option2_id, $option3_id, $option4_id);
		return $option;
	}
}
?>