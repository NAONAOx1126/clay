<?php
/**
 * 管理画面ユーザーのモデルです。
 *
 * @category  Model
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class Base_AdministratorModel extends DatabaseModel{
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("AdministratorsTable"), $values);
	}
	
	public function findByPrimaryKey($administrator_id){
		$this->findBy(array("site_id" => SITE_ID, "administrator_id" => $administrator_id));
	}

	public function findByLoginId($login_id){
		$this->findBy(array("site_id" => SITE_ID, "login_id" => $login_id));
	}
	
	public function site(){
		$loader = new PluginLoader();
		$site = $loader->loadModel("SiteModel");
		$site->findByPrimaryKey($this->site_id);
		return $site;		
	}
	
	public function hasRight($right){
		$rights = explode(",", $this->administrator_rights);
		if(!is_array($rights)){
			$rights = array();
		}
		if(array_search($right, $rights)){
			return true;
		}
		return false;
	}
}
?>