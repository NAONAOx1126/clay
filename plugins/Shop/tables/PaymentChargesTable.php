<?php
class Shop_PaymentChargesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_payment_charges", "shop");
	}
}
?>
