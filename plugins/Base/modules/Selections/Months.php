<?php
class Base_Selections_Months extends FrameworkModule{
	function execute($params){
		if(empty($_SERVER["ATTRIBUTES"]["SELECTION"][$params->get("key", "months")])){
			for($i = 1; $i <= 12; $i ++){
				$_SERVER["ATTRIBUTES"]["SELECTION"][$params->get("key", "months")][$i] = $i;
			}
		}
	}
}
?>
