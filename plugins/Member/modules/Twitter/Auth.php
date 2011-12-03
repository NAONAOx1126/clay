<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("TypeModel", "Members");

class Members_Twitter_Auth extends FrameworkModule{
	function execute($params){
		if($params->check("key") && $params->check("secret")){
			// Twitterアプリケーション申請で取得したコンシューマ key
			$consumer_key = $params->get("key");
			
			// Twitterアプリケーション申請で取得したコンシューマ secret
			$consumer_secret = $params->get("secret");
	
			if(empty($_SESSION[OAUTH_SESSION_KEY]["access_token"]) || empty($_SESSION[OAUTH_SESSION_KEY]["access_token_secret"])){
				// セッションを変数に格納
				$state = $_SESSION[OAUTH_SESSION_KEY]["state"];
				$session_token = $_SESSION[OAUTH_SESSION_KEY]["request_token"];
				$oauth_token = $_POST["oauth_token"];
				$section = $_POST["section"];
		
				if ($_REQUEST["oauth_token"] != NULL && $_SESSION[OAUTH_SESSION_KEY]["state"] === "start") {
					$_SESSION[OAUTH_SESSION_KEY]["state"] = $state = "returned";
				}
		
				switch ($state) {
					default:
						$to = new TwitterOAuth($consumer_key, $consumer_secret);
						$tok = $to->getRequestToken();
		
						$_SESSION[OAUTH_SESSION_KEY]["request_token"] = $token = $tok['oauth_token'];
						$_SESSION[OAUTH_SESSION_KEY]["request_token_secret"] = $tok['oauth_token_secret'];
						$_SESSION[OAUTH_SESSION_KEY]["state"] = "start";
		
						$request_link = $to->getAuthorizeURL($token, FALSE);
					
						$content = 'Click on the link to go to twitter to authorize your account.';
						$content .= '<br /><a href="'.$request_link.'">'.$request_link.'</a>';
						
						header("Location: $request_link");

						echo $content;
					
						exit;
		
					case 'returned':
						// もし access tokens がすでにセットされている場合は、 API call にいく
						if (empty($_SESSION[OAUTH_SESSION_KEY]["access_token"]) || empty($_SESSION[OAUTH_SESSION_KEY]["access_token_secret"]) || empty($_SESSION[OAUTH_SESSION_KEY]["user_id"])) {
							$to = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION[OAUTH_SESSION_KEY]["request_token"], $_SESSION[OAUTH_SESSION_KEY]["request_token_secret"]);
							$tok = $to->getAccessToken();
						
							// Tokenをセッションに格納 
							$_SESSION[OAUTH_SESSION_KEY]["access_token"] = $tok["oauth_token"];
							$_SESSION[OAUTH_SESSION_KEY]["access_token_secret"] = $tok["oauth_token_secret"];
		
							// Twitter名をセッションに格納
							$_SESSION[OAUTH_SESSION_KEY]["user_id"] = $tok["user_id"];
							$_SESSION[OAUTH_SESSION_KEY]["screen_name"] = $tok["screen_name"];
						}
				}
			}
		}
	}
}
?>