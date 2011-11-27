<?php
class Game_EpisodesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_episodes", "game");
	}
}
?>
