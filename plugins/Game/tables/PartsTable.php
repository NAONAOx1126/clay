<?php
class Game_PartsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_parts", "game");
	}
}
?>
