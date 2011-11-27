<?php
class Base_SiteConnectionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_site_connections", "base");
	}
}
?>
