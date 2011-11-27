<?php
class Game_GroupsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_groups", "game");
	}
}
?>
