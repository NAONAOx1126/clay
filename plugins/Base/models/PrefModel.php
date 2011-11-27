<?php
/**
 * 都道府県のデータモデルです。。
 *
 * @category  Model
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class PrefModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("PrefsTable"), $values);
	}
	
	function findByPrimaryKey($pref_id){
		$this->findBy(array("id" => $pref_id));
	}
	
	function __toString(){
		return $this->name;
	}
}
?>