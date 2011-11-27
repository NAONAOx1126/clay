<?php
class Member_CustomerTypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("member");
		parent::__construct("member_customer_types", "member");
	}
}
?>
