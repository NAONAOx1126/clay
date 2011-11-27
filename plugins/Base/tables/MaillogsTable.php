<?php
class Base_MaillogsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_maillogs", "base");
	}
}
?>
