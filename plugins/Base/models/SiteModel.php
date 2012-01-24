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
	/**
	 * コンストラクタ
	 */
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("SitesTable"), $values);
	}
	
	/**
	 * 主キーで検索する。
	 */
	public function findByPrimaryKey($site_id){
		$this->findBy(array("site_id" => $site_id));
	}
	
	/**
	 * サイトコードで検索する。
	 */
	public function findBySiteCode($site_code){
		$this->findBy(array("site_code" => $site_code));
	}
	
	/**
	 * ドメイン名で検索する。
	 */
	public function findByDomainName($domain_name){
		$this->findBy(array("domain_name" => $domain_name));
	}
	
	/**
	 * アクセス元ホストで検索する。
	 */
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

	public function companys(){
		$loader = new PluginLoader();
		$siteCompany = $loader->loadModel("SiteCompanyModel");
		$siteCompanys = $siteCompany->findAllByCompany($this->company_id);
		$result = array();
		foreach($siteCompanys as $siteCompany){
			$result[] = $siteCompany->company();
		}
		return $result;		
	}
	
	/**
	 * サイトのコネクションリストを取得する。
	 */
	public function connections(){
		$loader = new PluginLoader();
		$model = $loader->loadModel("SiteConnectionModel");
		return $model->findAllBySiteId($this->site_id);
	}
	
	/**
	 * サイトの個別設定リストを取得する。
	 */
	public function configures(){
		$loader = new PluginLoader();
		$model = $loader->loadModel("SiteConfigureModel");
		return $model->findAllBySiteId($this->site_id);
	}
}
?>