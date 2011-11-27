<?php
class Game_ClassesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_classes", "game");
	}
}
?>
