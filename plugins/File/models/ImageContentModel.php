<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("ImageContentsTable", "File");

/**
 * 顧客情報のモデルクラス
 */
class ImageContentModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new ImageContentsTable(), $values);
	}
	
	function findByPrimaryKey($image_content_id){
		$this->findBy(array("image_content_id" => $image_content_id));
	}
	
	function getCotentArrayByImage($image_id){
		$result = $this->findAllBy(array("image_id" => $image_id));
		$contents = array();
		if(is_array($result)){
			foreach($result as $data){
				$contents[$data->image_content_id] = new ImageContentModel($data);
			}
		}
		return $contents;
	}
}
?>