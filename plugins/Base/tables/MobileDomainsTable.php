<?php
class Base_MobileDomainsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_mobile_domains", "base");
	}
}
?>
