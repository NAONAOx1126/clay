<?php
class File_CsvContentsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("file");
		parent::__construct("file_csv_contents", "file");
	}
}
?>
