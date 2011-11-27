<?php
class Base_PrefsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_prefs", "base");
	}
}
?>
