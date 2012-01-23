<?php
/**
 * モバイルドメイン情報のデータモデルです。
 *
 * @category  Model
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class Base_MobileDomainModel extends DatabaseModel{
	/**
	 * コンストラクタ
	 */
	public function __construct($values = array()){
		$loader = new PluginLoader();
		parent::__construct($loader->loadTable("MobileDomainsTable"), $values);
	}
	
	/**
	 * 主キーでデータを取得する。
	 */
	public function findByPrimaryKey($mobile_domain_id){
		$this->findBy(array("mobile_domain_id" => $mobile_domain_id));
	}
	
	/**
	 * モバイルドメインでデータを取得する。
	 */
	public function findByMobileDomain($mobile_domain){
		$this->findBy(array("mobile_domain" => $mobile_domain));
	}
}
?>