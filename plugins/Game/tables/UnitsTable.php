<?php
class Game_UnitsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_units", "game");
	}
}
?>
