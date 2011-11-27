<?php
class Base_Selections_Days extends FrameworkModule{
	function execute($params){
		if(empty($_SERVER["ATTRIBUTES"]["SELECTION"][$params->get("key", "days")])){
			for($i = 1; $i <= 31; $i ++){
				$_SERVER["ATTRIBUTES"]["SELECTION"][$params->get("key", "days")][$i] = $i;
			}
		}
	}
}
?>
