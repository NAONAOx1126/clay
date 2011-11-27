<?php
class Member_CustomerOptionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("member");
		parent::__construct("member_customer_options", "member");
	}
}
?>
