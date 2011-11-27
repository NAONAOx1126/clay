<?php
class Member_CustomerDeliversTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("member");
		parent::__construct("member_customer_delivers", "member");
	}
}
?>
