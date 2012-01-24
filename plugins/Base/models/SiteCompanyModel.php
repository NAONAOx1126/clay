<?php
/**
 * サイトと所属組織の対応関係のモデルです。
 *
 * @category  Model
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class Base_SiteCompanyModel extends DatabaseModel{
	/**
	 * コンストラクタ
	 * @param $values モデルに初期設定する値
	 */
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("SiteCompanysTable"), $values);
	}
	
	/**
	 * 主キーでデータを取得する。
	 * @param $company_id 組織ID
	 */
	public function findByPrimaryKey($site_company_id){
		$this->findBy(array("site_company_id" => $site_company_id));
	}
	
	public function findBySiteCompany($site_id, $company_id){
		$this->findBy(array("site_id" => $site_id, "company_id" => $company_id));
	}
	
	public function findAllBySite($site_id){
		return $this->findAllBy(array("site_id" => $site_id));
	}

	public function findAllByCompany($company_id){
		return $this->findAllBy(array("company_id" => $company_id));
	}
	
	public function site(){
		$loader = new PluginLoader();
		$site = $loader->loadModel("SiteModel");
		$site->findByPrimaryKey($this->site_id);
		return $site;
	}

	public function company(){
		$loader = new PluginLoader();
		$company = $loader->loadModel("CompanyModel");
		$company->findByPrimaryKey($this->company_id);
		return $company;
	}
}
?>