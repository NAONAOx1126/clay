<?php
class Game_SlotsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_slots", "game");
	}
}
?>
