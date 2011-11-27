<?php
class Game_TeamsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_teams", "game");
	}
}
?>
