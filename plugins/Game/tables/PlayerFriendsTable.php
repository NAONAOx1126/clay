<?php
class Game_PlayerFriendsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_player_friends", "game");
	}
}
?>
