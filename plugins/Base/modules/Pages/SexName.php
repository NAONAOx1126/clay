<?php
class Base_Pages_SexName extends FrameworkModule{
	function execute($params){
		if($params->check("key")){
			$data = $_SERVER["ATTRIBUTES"][$params->get("key")];
			
			if(is_array($data) && !empty($data["sex"])){
				$data["sex_name"] = (($data["sex"] == "1")?"男性":(($data["sex"] == "2")?"女性":""));
			}
			if(is_object($data) && !empty($data->sex)){
				$data->sex_name = (($data->sex == "1")?"男性":(($data->sex == "2")?"女性":""));
			}
			
			$_SERVER["ATTRIBUTES"][$params->get("key")] = $data;
		}
	}
}
?>
