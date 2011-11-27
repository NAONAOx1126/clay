<?php
class File_CsvsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("file");
		parent::__construct("file_csvs", "file");
	}
}
?>
