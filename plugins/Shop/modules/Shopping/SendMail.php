<?php
// 共通処理を呼び出し。
LoadModel("Setting", "Shopping");
LoadModel("PrefModel");
LoadModel("MailTemplateModel");
LoadModel("CustomerModel", "Members");
LoadModel("CustomerTypeModel", "Members");
LoadModel("PaymentModel", "Shopping");
LoadModel("TempOrderModel", "Shopping");
LoadModel("TempOrderDetailModel", "Shopping");
LoadModel("OrderModel", "Shopping");
LoadModel("OrderDetailModel", "Shopping");
LoadModel("ProductModel", "Shopping");
LoadModel("ProductOptionModel", "Shopping");

class Shopping_Shopping_Contents extends FrameworkModule{
	function execute($params){
		// 受注完了メール送信
		if($params->check("mail")){
			$mailTemplate = new MailTemplateModel();
			$mailTemplate->findByPrimaryKey($params->get("mail"));
			$sendMail = new SendMail();
			$sendMail->setFrom($_SERVER["CONFIGURE"]["SITE"]["site_email"]);
			$sendMail->setTo($_SERVER["ATTRIBUTES"][$params->get("result", "order")]->order_email);
			$sendMail->setSubject($mailTemplate->subject);
			
			$body = $mailTemplate->body;
			$smarty = new Template();
			$smarty->assign("customer", $_SESSION[CUSTOMER_SESSION_KEY]);
			$smarty->assign("order", $_SERVER["ATTRIBUTES"][$params->get("result", "order")]);
			$body = $smarty->fetch("string:".$mailTemplate->body);
			$sendMail->addBody($body);
			$sendMail->send();
			$sendMail->reply();
		}
	}
}
?>
