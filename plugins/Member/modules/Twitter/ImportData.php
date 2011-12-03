<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");

class Members_Twitter_ImportData extends FrameworkModule{
	function execute($params){
		if(!empty($_SESSION[TWITTER_SESSION_KEY]) && $params->check("from")){
			$from = $params->get("from");
			$key = $params->get("key", $from);
			if(!isset($_SESSION["INPUT_DATA"][$key])){
				$_SESSION["INPUT_DATA"][$key] = $_SESSION[TWITTER_SESSION_KEY]->$from;
			}
		}
		$_SERVER["INPUT_DATA"] = $_SESSION["INPUT_DATA"];
	}
}
?>