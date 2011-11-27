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
LoadTable("CategoryTypesTable", "Shopping");

/**
 * 決済方法のモデルクラス
 */
class CategoryTypeModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new CategoryTypesTable(), $values);
	}
	
	function findByPrimaryKey($category_type_id){
		$this->findBy(array("category_type_id" => $category_type_id));
	}
}
?>