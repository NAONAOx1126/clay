<?php
class Base_AdministratorsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_administrators", "base");
	}
}
?>
