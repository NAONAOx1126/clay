<?php
/**
 * CSVファイルのファイル情報を扱うモデルです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Models
 * @package   File
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
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