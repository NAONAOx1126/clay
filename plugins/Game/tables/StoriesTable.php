<?php
class Game_StoriesTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_stories", "game");
	}
}
?>
