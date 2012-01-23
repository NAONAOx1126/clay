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
class Base_CompanyOperatorModel extends DatabaseModel{
	/**
	 * コンストラクタ
	 */
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("CompanyOperatorsTable"), $values);
	}
	
	/**
	 * 主キーでオペレータを検索する。
	 */
	public function findByPrimaryKey($operator_id){
		$this->findBy(array("operator_id" => $operator_id));
	}

	/**
	 * オペレータのログインIDでデータを検索する。
	 */
	public function findByLoginId($login_id){
		$this->findBy(array("login_id" => $login_id));
	}
	
	/**
	 * 組織のIDでオペレータのデータを検索する。
	 */
	public function findAllByCompanyId($company_id){
		return $this->findAllBy(array("company_id" => $company_id));
	}
	
	/**
	 * オペレータの管理対象となっているサイトを検索する。
	 */
	public function sites(){
		$loader = new PluginLoader();
		$site = $loader->loadModel("SiteModel");
		if(!$this->isSuper()){
			$site->findByPrimaryKey($this->site_id);
			$sites = array($site);
		}else{
			$sites = $site->findAllBy(array());
		}
		return $sites;
	}
	
	/**
	 * 特権オペレータかどうかを調べる。
	 */
	public function isSuper(){
		return $this->super_flg;
	}
	
	/**
	 * オペレータの所属する組織のデータを取得する。
	 */
	public function company(){
		$loader = new PluginLoader();
		$company = $loader->loadModel("CompanyModel");
		$company->findByPrimaryKey($this->company_id);
		return $company;
		
	}
}
?>