<?php
$memberPluginLoader = new PluginLoader("Member");
$memberPluginLoader->LoadModel("Setting");

class Member_Agreement extends FrameworkModule{
	function execute($param){
		if($_POST["agreement"] != "1"){
			throw new InvalidException(array("このページにアクセスするためには同意が必要です。"));
		}
	}
}
?>
