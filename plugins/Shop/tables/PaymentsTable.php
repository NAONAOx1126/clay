<?php
class Shop_PaymentsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_payments", "shop");
	}
}
?>
