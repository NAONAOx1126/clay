<?php
class File_ImageContentsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("file");
		parent::__construct("file_image_contents", "file");
	}
}
?>
