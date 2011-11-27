<?php
class Base_Selections_Years extends FrameworkModule{
	function execute($params){
		// デフォルトは当年から当年まで
		$start = date("Y") - $params->get("past", "0");
		$end = $start + $params->get("term", $params->get("past", "0"));
		
		if(empty($_SERVER["ATTRIBUTES"]["SELECTION"][$params->get("key", "years")])){
			for($i = $start; $i <= $end; $i ++){
				$_SERVER["ATTRIBUTES"]["SELECTION"][$params->get("key", "years")][$i] = $i;
			}
		}
	}
}
?>
