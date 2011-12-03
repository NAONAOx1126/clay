<?php
class Base_CompanysTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_companys", "base");
	}
}
?>
