<?php
class Base_Pages_ValueMerge extends FrameworkModule{
	function execute($params){
		if($params->check("key") && $params->check("param") && $params->check("result") && $params->check("split")){
			$keys = explode(",", $params->get("param"));
			$values = array();
			foreach($keys as $key){
				$key = trim($key);
				if(is_array($_SERVER["ATTRIBUTES"][$params->get("key")]) && !empty($_SERVER["ATTRIBUTES"][$params->get("key")][$key])){
					if(!empty($_SERVER["ATTRIBUTES"][$params->get("key")][$key])){
						$values[] = $_SERVER["ATTRIBUTES"][$params->get("key")][$key];
					}
				}
				if(is_object($_SERVER["ATTRIBUTES"][$params->get("key")]) && !empty($_SERVER["ATTRIBUTES"][$params->get("key")]->$key)){
					if(!empty($_SERVER["ATTRIBUTES"][$params->get("key")]->$key)){
						$values[] = $_SERVER["ATTRIBUTES"][$params->get("key")]->$key;
					}
				}
			}
			$key = $params->get("result");
			$value = implode($params->get("split"), $values);
			if(is_array($_SERVER["ATTRIBUTES"][$params->get("key")])){
				$_SERVER["ATTRIBUTES"][$params->get("key")][$key] = $value;
			}
			if(is_object($_SERVER["ATTRIBUTES"][$params->get("key")])){
				$_SERVER["ATTRIBUTES"][$params->get("key")]->$key = $value;
			}
		}
	}
}
?>
