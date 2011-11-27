<?php
/**
 * 顧客情報のモデルクラス
 */
class File_CsvContentModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("File");
		parent::__construct($loader->loadTable("CsvContentsTable"), $values);
	}
	
	function findByPrimaryKey($csv_content_id){
		$this->findBy(array("csv_content_id" => $csv_content_id));
	}
	
	function getCotentArrayByCsv($csv_id){
		$result = $this->findAllBy(array("csv_id" => $csv_id), "`order`");
		$contents = array();
		if(is_array($result)){
			foreach($result as $data){
				$contents[$data->csv_content_id] = new File_CsvContentModel($data);
			}
		}
		return $contents;
	}
}
?>