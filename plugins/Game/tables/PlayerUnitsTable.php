<?php
class Game_PlayerUnitsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_player_units", "game");
	}
}
?>
