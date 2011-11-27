<?php
class Game_TypesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_types", "game");
	}
}
?>
