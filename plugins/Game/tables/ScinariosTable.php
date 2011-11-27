<?php
class Game_ScinariosTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_scinarios", "game");
	}
}
?>
