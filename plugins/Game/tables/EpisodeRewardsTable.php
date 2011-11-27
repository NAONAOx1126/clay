<?php
class Game_EpisodeRewardsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_episode_rewards", "game");
	}
}
?>
