<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("TypeModel", "Members");

class Members_Twitter_GetUser extends FrameworkModule{
	function execute($params){
		if(!empty($_SESSION[OAUTH_SESSION_KEY]["user_id"]) && !isset($_SESSION[TWITTER_SESSION_KEY])){
			$twitter = new TwitterOAuth($params->get("key"), $params->get("secret"), $_SESSION[OAUTH_SESSION_KEY]["access_token"], $_SESSION[OAUTH_SESSION_KEY]["access_token_secret"]);
			$twitter->format = "xml";
			$xml = $twitter->get("http://twitter.com/users/show.xml", array("id" => $_SESSION[OAUTH_SESSION_KEY]["user_id"]));
			$http_info = $twitter->http_info;
			if ($http_info["http_code"] == "200" && !empty($xml)){
				$userTemp = simplexml_load_string($xml);
				$user = (object) NULL;
				$user->id = (string) $userTemp->id;
				$user->name = (string) $userTemp->name;
				$user->screen_name = (string) $userTemp->screen_name;
				$user->location = (string) $userTemp->location;
				$user->description = (string) $userTemp->description;
				$user->profile_image_url = (string) $userTemp->profile_image_url;
				$user->original_image_url = str_replace("_normal.", ".", $user->profile_image_url);
				$user->url = (string) $userTemp->url;
				$user->followers_count = (string) $userTemp->followers_count;
				$user->friends_count = (string) $userTemp->friends_count;
				$user->created_at = (string) $userTemp->created_at;
				$user->favourites_count = (string) $userTemp->favourites_count;
				$user->utc_offset = (string) $userTemp->utc_offset;
				$user->time_zone = (string) $userTemp->time_zone;
				$_SESSION[TWITTER_SESSION_KEY] = $user;
			}
		}
		$_SERVER["Twitter"] = $_SESSION[TWITTER_SESSION_KEY];
	}
}
?>