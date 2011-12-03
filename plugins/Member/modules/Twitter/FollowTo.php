<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("TypeModel", "Members");

class Members_Twitter_FollowTo extends FrameworkModule{
	function execute($params){
		if(!empty($_SESSION[OAUTH_SESSION_KEY]["user_id"]) && $params->check("target")){
			$twitter = new TwitterOAuth($params->get("key"), $params->get("secret"), $_SESSION[OAUTH_SESSION_KEY]["access_token"], $_SESSION[OAUTH_SESSION_KEY]["access_token_secret"]);
			$twitter->format = "xml";
			$xml = $twitter->post("http://api.twitter.com/1/friendships/create.xml", array("screen_name" => $params->get("target")));
			$http_info = $twitter->http_info;
			if ($http_info["http_code"] != "200"){
				if($params->check("error")){
					throw new InvalidException(array("対象のTwitterアカウントが無いか、既にフォローしています。"));
				}
			}
		}
	}
}
?>