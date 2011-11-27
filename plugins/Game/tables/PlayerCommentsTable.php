<?php
class Game_PlayerCommentsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_player_comments", "game");
	}
}
?>
