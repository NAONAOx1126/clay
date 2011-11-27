<?php
class Base_Pages_CheckToComment extends FrameworkModule{
	function execute($params){
		if($params->check("key") && $params->check("param") && $params->check("result") && $params->check("text")){			
			if(is_array($_SERVER["ATTRIBUTES"][$params->get("key")]) && !empty($_SERVER["ATTRIBUTES"][$params->get("key")][$params->get("param")])){
				if($_SERVER["ATTRIBUTES"][$params->get("key")][$params->get("param")] == "1"){
					$_SERVER["ATTRIBUTES"][$params->get("key")][$params->get("result")] = $params->get("text");
				}
			}
			$source = $params->get("param");
			$destination = $params->get("result");
			if(is_object($_SERVER["ATTRIBUTES"][$params->get("key")]) && !empty($_SERVER["ATTRIBUTES"][$params->get("key")]->$source)){
				if($_SERVER["ATTRIBUTES"][$params->get("key")]->$source == "1"){
					$_SERVER["ATTRIBUTES"][$params->get("key")]->$destination = $params->get("text");
				}
			}
		}
	}
}
?>
