<?php
class Base_Pages_ValuesToInput extends FrameworkModule{
	function execute($params){
		if($params->check("key")){
			if(!empty($_SERVER["ATTRIBUTES"][$params->get("key")])){
				if($_SERVER["ATTRIBUTES"][$params->get("key")] instanceof DatabaseModel){
					$values = $_SERVER["ATTRIBUTES"][$params->get("key")]->values;
				}else{
					$values = (array) $_SERVER["ATTRIBUTES"][$params->get("key")];
				}
				foreach($values as $name => $value){
					if(!isset($_SESSION["INPUT_DATA"][$name])){
						$_SESSION["INPUT_DATA"][$name] = $value;
					}
				}
			}
			$_SERVER["INPUT_DATA"] = $_SESSION["INPUT_DATA"];
		}
	}
}
?>
