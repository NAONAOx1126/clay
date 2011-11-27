<?php
/**
 * サイト情報のデータモデルです。
 *
 * @category  Model
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class Base_SiteModel extends DatabaseModel{
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("SitesTable"), $values);
	}
	
	public function findByPrimaryKey($site_id){
		$this->findBy(array("site_id" => $site_id));
	}
	
	public function findBySiteCode($site_code){
		$this->findBy(array("site_code" => $site_code));
	}
	
	public function findByDomainName($domain_name){
		$this->findBy(array("domain_name" => $domain_name));
	}
	
	public function findByHostName(){
		$select = new DatabaseSelect($this->access);
		$select->addColumn($this->access->_W);
		$select->addWhere("? LIKE CONCAT('%', ".$this->access->domain_name.")", array($_SERVER["SERVER_NAME"]));
		$select->addOrder("LENGTH(".$this->access->domain_name.")", true);
		$result = $select->execute();

		if(count($result) > 0){
			$this->setValues($result[0]);
			return true;
		}
		return false;
	}
	
	public function connections(){
		$loader = new PluginLoader();
		$model = $loader->loadModel("SiteConnectionModel");
		return $model->findAllBySiteId($this->site_id);
	}
	
	public function configures(){
		$loader = new PluginLoader();
		$model = $loader->loadModel("SiteConfigureModel");
		return $model->findAllBySiteId($this->site_id);
	}
}
?>