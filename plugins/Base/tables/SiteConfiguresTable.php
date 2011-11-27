<?php
class Base_SiteConfiguresTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_site_configures", "base");
	}
}
?>
