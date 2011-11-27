<?php
class Game_ChaptersTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_chapters", "game");
	}
}
?>
