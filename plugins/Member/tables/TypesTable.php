<?php
class Member_TypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("member");
		parent::__construct("member_types", "member");
	}
}
?>
