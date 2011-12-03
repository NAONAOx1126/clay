<?php
class Base_CompanyOperatorsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_company_operators", "base");
	}
}
?>
