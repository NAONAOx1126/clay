<?php
class Member_SerialLogsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("member");
		parent::__construct("member_serial_logs", "member");
	}
}
?>
