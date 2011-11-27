<?php
class Shop_OptionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("shop");
		parent::__construct("shop_options", "shop");
	}
}
?>
