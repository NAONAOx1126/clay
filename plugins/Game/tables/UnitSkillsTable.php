<?php
class Game_UnitSkillsTable extends DatabaseTable{
	function __construct(){
		$this->db = DBFactory::getConnection("game");
		parent::__construct("game_unit_skills", "game");
	}
}
?>
