<?php
class Shop_FlagsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_flags", "shop");
	}
}
?>
