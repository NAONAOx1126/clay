<?php
class Game_PlayerProgressionTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_player_progression", "game");
	}
}
?>
