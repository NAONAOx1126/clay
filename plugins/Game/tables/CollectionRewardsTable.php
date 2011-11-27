<?php
class Game_CollectionRewardsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_collection_rewards", "game");
	}
}
?>
