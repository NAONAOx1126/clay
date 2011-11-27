<?php
/**
 * 商品カテゴリのデータモデルです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Extensions
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

// この処理で使用するテーブルモデルをインクルード
LoadTable("CategoriesTable", "Shopping");
LoadTable("CategoryTypesTable", "Shopping");
LoadTable("ProductCategoriesTable", "Shopping");

LoadModel("ProductCategoryModel", "Shopping");
LoadModel("CategoryTypeModel", "Shopping");

/**
 * 決済方法のモデルクラス
 */
class CategoryModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new CategoriesTable(), $values);
	}
	
	function findByPrimaryKey($category_id){
		$this->findBy(array("category_id" => $category_id));
	}

	function findAllByType($category_type_id){
		$result = $this->findAllBy(array("category_type_id" => $category_type_id));
		$categories = array();
		if(is_array($result)){
			foreach($result as $data){
				$categories[$data["category_id"]] = new CategoryModel($data);
			}
		}
		return $categories;	
	}
	
	function getCategoryArrayByType($category_type_id){
		$result = $this->findAllBy(array("category_type_id" => $category_type_id));
		$categories = array();
		if(is_array($result)){
			foreach($result as $data){
				$categories[$data["category_id"]] = new CustomerOptionModel($data);
			}
		}
		return $categories;	
	}
	
	function getCategoryPageByType($option, $category_type_id){
		// この機能で使用するテーブルモデルを初期化
		$categories = new CategoriesTable();
		$categoryTypes = new CategoryTypesTable();
		$productCategories = new ProductCategoriesTable();

		// ジャンルのリストを取得する処理
		$select = new DatabaseSelect($categories);
		$select->addColumn($categoryTypes->category_type)->addColumn($categories->_W);
		$select->joinInner($categoryTypes, array($categories->category_type_id." = ".$categoryTypes->category_type_id));
		if(!empty($category_type_id)){
			$select->addWhere($categoryTypes->category_type_id." = ".$category_type_id);
		}
		if(!empty($_POST["parent_category_id"])){
			$select->addWhere($categories->parent_category_id." = ?", array($_POST["parent_category_id"]));
		}
		if(!empty($_POST["product_id"])){
			$select->joinInner($productCategories, array($categories->category_id." = ".$productCategories->category_id));
			$select->addWhere($productCategories->product_id." = ?", array($_POST["product_id"]));
		}
		$select->addOrder($categories->sort_order);
		$result = $select->executePager($option);
		
		return $result;
	}
	
	function products(){
		$model = new ProductCategoryModel();
		$products = $model->findAllByCategory($this->category_id);
		return $products;
	}
	
	function productsPage($option){
		$model = new ProductCategoryModel();
		$pager = $model->pager($option);
		$products = $pager->findAllBy(array("category_id" => $this->category_id));
		return $products;
	}
	
	function type(){
		$type = new CateogryTypeModel();
		$type->findByPrimaryKey($this->category_type_id);
		return $type;
	}
}
?>