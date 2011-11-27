<?php
class Base_ZipsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_zips", "base");
	}
}
?>
