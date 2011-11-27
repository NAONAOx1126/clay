<?php
class Game_LotteriesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_lotteries", "game");
	}
}
?>
