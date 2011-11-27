<?php
class Game_PlayerItemsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_player_items", "game");
	}
}
?>
