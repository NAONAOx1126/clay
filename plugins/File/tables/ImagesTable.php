<?php
class File_ImagesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("file");
		parent::__construct("file_images", "file");
	}
}
?>
