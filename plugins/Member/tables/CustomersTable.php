<?php
class Member_CustomersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("member");
		parent::__construct("member_customers", "member");
	}
}
?>
