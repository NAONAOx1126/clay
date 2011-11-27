<?php
LoadModel("PrefModel");

class Base_Pages_PrefName extends FrameworkModule{
	function execute($params){
		if($params->check("key")){
			$pref = new PrefModel();
			$data = $_SERVER["ATTRIBUTES"][$params->get("key")];
			
			if(is_array($data) && !empty($data["pref"])){
				$pref->findByPrimaryKey($data["pref"]);
				$data["pref_name"] = $pref->name;
			}
			if(is_object($data) && !empty($data->pref)){
				$pref->findByPrimaryKey($data->pref);
				$data->pref_name = $pref->name;
			}
			
			$_SERVER["ATTRIBUTES"][$params->get("key")] = $data;
		}
	}
}
?>
