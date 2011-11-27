<?php
// この処理で使用するテーブルモデルをインクルード
LoadModel("MailTemplateModel");

class Default_Forms_SendFormMail extends FrameworkModule{
	function execute($params){
		// 引数を処理する。
		$template = $params->get("template");
		
		// templateが設定されていて、hiddenに設定されたパラメータがPOSTで渡されている場合に処理を実行
		if(!empty($template)){
			// ローダーを初期化
			$loader = new PluginLoader();
			// この機能で使用するテーブルモデルを初期化
			$mailTemplate = $loader->loadModel("MailTemplateModel");
			
			// テンプレートデータを取得
			$mailTemplate->findByPrimaryKey($template);
			
			// データが取得できた場合のみ処理を実行
			if($mailTemplate->template_code == $template){
				$mail = new SendMail();
				$mail->setFrom($_POST[$params->get("email", "email")]);
				$mail->setTo($_SERVER["CONFIGURE"]["site_email"], $group["manager_group_name"]);
				$mail->setSubject($subject);
				$mail->addBody($body);
				$mail->send();
				if($index == 0){
					$mail->reply();
				}
			}
				
			// メールボディを作成
			$subject = "";
			$body = "";
			if(!empty($result)){
				$subject .= $result[0]["subject"];
				$body .= $result[0]["header"];
				$body .= "\r\n-----------------------------------\r\n";
			}
			foreach($_POST[$hidden] as $form){
				list($form_id, $form_name, $sp) = explode(":", $form);
				$body .= "■".$form_name."\r\n     ";
				$body .= (is_array($_POST[$form_id])?implode($sp, $_POST[$form_id]):$_POST[$form_id])."\r\n\r\n";
			}
			if(!empty($result)){
				$body .= "\r\n-----------------------------------\r\n";
				$body .= $result[0]["footer"];
			}
			
			// この機能で使用するテーブルモデルを初期化
			$siteManagerGroups = new SiteManagerGroupsTable();
			$managerGroups = new ManagerGroupsTable();
	
			// 管理グループのリストを取得する処理
			$select = new DatabaseSelect($siteManagerGroups);
			$select->addColumn($managerGroups->_W);
			$select->joinInner($managerGroups, array($siteManagerGroups->manager_group_id." = ".$managerGroups->manager_group_id));
			$select->addOrder($managerGroups->manager_group_id);
			$select->addWhere($siteManagerGroups->site_id." = ?", array($_SERVER["CONFIGURE"]["SITE"]["site_id"]));
			$result = $select->execute();

			foreach($result as $index => $group){
				$mail = new SendMail();
				$mail->setFrom($_POST[$params->get("email", "email")]);
				$mail->setTo($group["email"], $group["manager_group_name"]);
				$mail->setSubject($subject);
				$mail->addBody($body);
				$mail->send();
				if($index == 0){
					$mail->reply();
				}
			}
		}
	}
}
?>
