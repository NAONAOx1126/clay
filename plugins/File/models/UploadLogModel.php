<?php
/**
 * ファイルアップロードのログ情報を扱うモデルです。
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
class File_UploadLogModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("File");
		parent::__construct($loader->loadTable("UploadLogsTable"), $values);
	}
	
	function findByPrimaryKey($upload_id){
		$this->findBy(array("upload_id" => $upload_id));
	}
}
?>