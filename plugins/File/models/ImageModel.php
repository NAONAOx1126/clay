<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("ImagesTable", "File");

/**
 * 顧客情報のモデルクラス
 */
class ImageModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new ImagesTable(), $values);
	}
	
	function findByPrimaryKey($image_id){
		$this->findBy(array("image_id" => $image_id));
	}
	
	function findByImageCode($image_code){
		$this->findBy(array("image_code" => $image_code));
	}
}
?>