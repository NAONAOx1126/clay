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
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("CompanysTable"), $values);
	}
	
	public function findByPrimaryKey($company_id){
		$this->findBy(array("company_id" => $company_id));
	}

	public function operators(){
		$loader = new PluginLoader();
		$companyOperator = $loader->loadModel("CompanyOperatorModel");
		$companyOperators = $companyOperator->findAllByCompanyId($this->company_id);
		return $companyOperator;		
	}
}
?>