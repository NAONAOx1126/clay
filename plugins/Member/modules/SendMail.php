<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("MailTemplateModel");

/**
 * 携帯の個体番号でのログインを実行するモジュールです。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_SendMail extends FrameworkModule{
	function execute($params){
		if($params->check("mail")){
			// トランザクションデータベースの取得
			$db = DBFactory::getLocal();// トランザクションの開始
			$db->beginTransaction();
			
			try{
				// 登録完了メール送信
				if($params->check("html")){
					$sendMail = new SendPCHtmlMail();
				}else{
					$sendMail = new SendMail();
				}
				$emailKey = $params->get("email", "email");
				$smarty = new Template();
				foreach($_SERVER as $name =>$value){
					$smarty->assign($name, $value);
				}
				
				// メールの送信元（サイトメールアドレス）を設定
				$sendMail->setFrom($_SERVER["CONFIGURE"]["SITE"]["site_email"], $_SERVER["CONFIGURE"]["SITE"]["site_name"]);
				// メールの送信先を設定
				$sendMail->setTo($_SERVER["ATTRIBUTES"][$params->get("result", "customer")]->$emailKey);
				// テキストメールの設定からタイトルと本文を取得
				$mailTemplate = new MailTemplateModel();
				$mailTemplate->findByPrimaryKey($params->get("mail"));
				$sendMail->setSubject($mailTemplate->subject);
				$body = $smarty->fetch("string:".$mailTemplate->body);
				// HTMLメールのテンプレートから本文を取得
				if($params->check("html")){
					$sendMail->addExtBody($body);
					$mailHtmlTemplate = new MailTemplateModel();
					$mailHtmlTemplate->findByPrimaryKey($params->get("html"));
					$body = $smarty->fetch("string:".$mailHtmlTemplate->body);
				}
				$sendMail->addBody($body);
				$sendMail->send();
				$sendMail->reply();
			}catch(Exception $ex){
				unset($_POST["regist"]);
				$db->rollBack();
				throw $ex;
			}
		}
	}
}
?>