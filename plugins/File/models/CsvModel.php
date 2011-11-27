<?php
/**
 * 顧客情報のモデルクラス
 */
class File_CsvModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("File");
		parent::__construct($loader->loadTable("CsvsTable"), $values);
	}
	
	function findByPrimaryKey($csv_id){
		$this->findBy(array("csv_id" => $csv_id));
	}
	
	function findByCsvCode($csv_code){
		$this->findBy(array("csv_code" => $csv_code));
	}
}
?>