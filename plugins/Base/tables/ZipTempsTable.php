<?php
class Base_ZipTempsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_zip_temps", "base");
	}
}
?>
