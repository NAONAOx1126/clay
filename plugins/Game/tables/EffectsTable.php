<?php
class Game_EffectsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_effects", "game");
	}
}
?>
