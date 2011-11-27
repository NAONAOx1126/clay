<?php
// パラメータを変数に格納
$template_code_prefix = $argv[0];
$sendmail_term = $argv[1];

// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");
LoadModel("MailTemplateModel");

// パラメータからメールテンプレートを取得
$mailTemplate = new MailTemplateModel();
$mailTemplate->findByPrimaryKey($template_code_prefix.$sendmail_term);
$mailHtmlTemplate = new MailTemplateModel();
$mailHtmlTemplate->findByPrimaryKey($template_code_prefix.$sendmail_term."_html");

// テンプレートが取れた場合のみメールを送信する。
if($mailTemplate->template_code ==  $template_code_prefix.$sendmail_term){
	// 該当の時間に登録時間が一致するユーザーを抽出する。
	$condition = array(">=:create_time" => date("Y-m-d H:00:00", strtotime("-".($sendmail_term + 1)." hour")), "<:create_time" => date("Y-m-d H:00:00", strtotime("-".($sendmail_term)." hour")));
	//print_r($condition);
	
	// カスタマモデルを使用して顧客情報を取得
	$customer = new CustomerModel();
	$customers = $customer->getCustomersArray($condition);
	
	foreach($customers as $customer){
		//echo $customer["customer_id"]."：".$customer["mei"]."(".$customer["create_time"].")<br>\r\n";
		
		// 登録完了メール送信
		if($mailHtmlTemplate->template_code == $template_code_prefix.$sendmail_term."_html"){
			$sendMail = new SendPCHtmlMail();
		}else{
			$sendMail = new SendMail();
		}
		$smarty = new Template();
		foreach($customer as $name =>$value){
			$smarty->assign($name, $value);
		}
		
		// メールの送信元（サイトメールアドレス）を設定
		$sendMail->setFrom($_SERVER["CONFIGURE"]["SITE"]["site_email"], $_SERVER["CONFIGURE"]["SITE"]["site_name"]);
		// メールの送信先を設定
		$sendMail->setTo($customer["email"], $customer["sei"].$customer["mei"]);
		// テキストメールの設定からタイトルと本文を取得
		$sendMail->setSubject($mailTemplate->subject);
		$body = $smarty->fetch("string:".$mailTemplate->body);
		// HTMLメールのテンプレートから本文を取得
		if($mailHtmlTemplate->template_code == $template_code_prefix.$sendmail_term."_html"){
			$sendMail->addExtBody($body);
			$body = $smarty->fetch("string:".$mailHtmlTemplate->body);
		}
		$sendMail->addBody($body);
		//print_r($sendMail);
		$sendMail->send();
		$sendMail->reply();
	}
}
?>
