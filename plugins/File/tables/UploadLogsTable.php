<?php
class File_UploadLogsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("file");
		parent::__construct("file_upload_logs", "file");
	}
}
?>
