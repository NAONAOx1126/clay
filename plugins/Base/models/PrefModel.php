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
class Base_PrefModel extends DatabaseModel{
	/**
	 * コンストラクタ
	 */
	function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("PrefsTable"), $values);
	}
	
	/**
	 * 主キーでデータを検索する。
	 */
	function findByPrimaryKey($pref_id){
		$this->findBy(array("id" => $pref_id));
	}
	
	/**
	 * モデル自体を都道府県の名前文字列として扱えるようにする。
	 */
	function __toString(){
		return $this->name;
	}
}
?>