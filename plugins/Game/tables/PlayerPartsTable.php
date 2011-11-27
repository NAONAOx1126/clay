<?php
class Game_PlayerPartsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_player_parts", "game");
	}
}
?>
