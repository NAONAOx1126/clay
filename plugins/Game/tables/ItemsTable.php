<?php
class Game_ItemsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_items", "game");
	}
}
?>
