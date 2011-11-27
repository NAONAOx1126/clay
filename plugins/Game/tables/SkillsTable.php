<?php
class Game_SkillsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_skills", "game");
	}
}
?>
