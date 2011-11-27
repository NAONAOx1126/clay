<?php
class Base_Pages_InputMerge extends FrameworkModule{
	function execute($params){
		if($params->check("param") && $params->check("result") && $params->check("split")){
			$keys = explode(",", $params->get("param"));
			$values = array();
			foreach($keys as $key){
				$key = trim($key);
				if(is_array($_SERVER["INPUT_DATA"]) && !empty($_SERVER["INPUT_DATA"][$key])){
					if(!empty($_SERVER["INPUT_DATA"][$key])){
						$values[] = $_SERVER["INPUT_DATA"][$key];
					}
				}
				if(is_object($_SERVER["INPUT_DATA"]) && !empty($_SERVER["INPUT_DATA"]->$key)){
					if(!empty($_SERVER["INPUT_DATA"]->$key)){
						$values[] = $_SERVER["INPUT_DATA"]->$key;
					}
				}
			}
			$key = $params->get("result");
			$value = implode($params->get("split"), $values);
			if(is_array($_SERVER["INPUT_DATA"])){
				$_SERVER["INPUT_DATA"][$key] = $value;
			}
			if(is_object($_SERVER["INPUT_DATA"])){
				$_SERVER["INPUT_DATA"]->$key = $value;
			}
		}
	}
}
?>
