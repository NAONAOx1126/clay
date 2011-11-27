<?php
class Game_LotteryRewardsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_lottery_rewards", "game");
	}
}
?>
