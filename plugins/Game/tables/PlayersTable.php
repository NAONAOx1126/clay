<?php
class Game_PlayersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_players", "game");
	}
}
?>
