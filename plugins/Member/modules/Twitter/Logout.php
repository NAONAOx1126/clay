<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");

/**
 * Twitterでログインしたセッションをクローズするためのモジュールです。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_Twitter_Logout extends FrameworkModule{
	function execute($params){
		if($params->check("key") && $params->check("secret")){
			if(!empty($_POST["logout"])){
				// Twitterアプリケーション申請で取得したコンシューマ key
				$consumer_key = $params->get("key");
				
				// Twitterアプリケーション申請で取得したコンシューマ secret
				$consumer_secret = $params->get("secret");
	
				// アクセストークンが発行されている場合にはログアウト処理
				if (!empty($_SESSION[OAUTH_SESSION_KEY]["access_token"]) && !empty($_SESSION[OAUTH_SESSION_KEY]["access_token_secret"])) {
					// Twitterのログイン状態を無効にする。
					$twitter = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION[OAUTH_SESSION_KEY]["access_token"], $_SESSION[OAUTH_SESSION_KEY]["access_token_secret"]);
					$twitter->format = "xml";
					$xml = $twitter->post("http://api.twitter.com/1/account/end_session.xml", array());
	
					// システム側のセッションをクリアする。
					unset($_SESSION[CUSTOMER_SESSION_KEY]);
					unset($_SESSION[TWITTER_SESSION_KEY]);
					unset($_SESSION[OAUTH_SESSION_KEY]);
				}
			}
		}
	}
}
?>