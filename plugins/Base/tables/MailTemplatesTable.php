<?php
class Base_MailTemplatesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("base");
		parent::__construct("base_mail_templates", "base");
	}
}
?>
