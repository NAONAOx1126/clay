<?php
class Base_SiteCompanysTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_site_companys", "base");
	}
}
?>
