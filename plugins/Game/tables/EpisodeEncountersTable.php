<?php
class Game_EpisodeEncountersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_episode_encounters", "game");
	}
}
?>
