<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerModel", "Members");

/**
 * 必須入力のチェックを行うCheckパッケージのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Check
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

class Members_Checks_UniqueEmail extends FrameworkModule{
	function execute($params){
		if(!is_array($_SERVER["ERRORS"])){
			$_SERVER["ERRORS"] = array();
		}
		
		$customer = new CustomerModel();
		if(empty($_SERVER["ERRORS"]["email"]) && !empty($_POST["email"])){
			$customer->findByEmail($_POST["email"]);
			if(!empty($customer->customer_id) && $_POST["customer_id"] != $customer->customer_id){
				$_SERVER["ERRORS"]["email"] = "このメールアドレスは既に登録されております。";
			}else{
				$customer->findByEmailMobile($_POST["email"]);
				if(!empty($customer->customer_id) && $_POST["customer_id"] != $customer->customer_id){
					$_SERVER["ERRORS"]["email"] = "このメールアドレスは既に登録されております。";
				}
			}
		}
		if(empty($_SERVER["ERRORS"]["email"]) && !empty($_POST["email_mobile"])){
			$customer->findByEmail($_POST["email_mobile"]);
			if(!empty($customer->customer_id) && $_POST["customer_id"] != $customer->customer_id){
				$_SERVER["ERRORS"]["email"] = "このメールアドレスは既に登録されております。";
			}else{
				$customer->findByEmailMobile($_POST["email_mobile"]);
				if(!empty($customer->customer_id) && $_POST["customer_id"] != $customer->customer_id){
					$_SERVER["ERRORS"]["email"] = "このメールアドレスは既に登録されております。";
				}
			}
		}
	}
}
?>
