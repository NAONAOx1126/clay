<?php
class Game_ClassProgressionTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_class_progression", "game");
	}
}
?>
