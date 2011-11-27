<?php
class Base_SessionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_sessions", "base");
	}
}
?>
