<?php
class Game_CollectionsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_collections", "game");
	}
}
?>
