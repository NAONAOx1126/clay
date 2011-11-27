<?php
class Base_SitesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_sites", "base");
	}
}
?>
