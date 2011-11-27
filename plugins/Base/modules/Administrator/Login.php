<?php
/**
 * ### Base.Administrator.Login
 * 管理画面のログイン処理を実行する。
 * 
 * @category  Module
 * @package   Administrator
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class Base_Administrator_Login extends FrameworkModule{
	function execute($params){
		$loader = new PluginLoader();
		if(empty($_SESSION["ADMINISTRATOR"])){
			// 管理者モデルを取得する。
			$administrator = $loader->loadModel("AdministratorModel");
	
			// 渡されたログインIDでレコードを取得する。
			$administrator->findByLoginId($_POST["login_id"]);
			
			// ログインIDに該当するアカウントが無い場合
			if(!($administrator->administrator_id > 0)){
				throw new InvalidException(array("ログイン情報が正しくありません。"));
			}
			
			// 保存されたパスワードと一致するか調べる。
			if($administrator->password != sha1($administrator->login_id.":".$_POST["password"])){
				throw new InvalidException(array("ログイン情報が正しくありません。"));
			}
			
			// ログインに成功した場合には管理者情報をセッションに格納する。
			$_SESSION["ADMINISTRATOR"] = $administrator->values;
		}
		// 管理者モデルを復元する。
		$administrator = $loader->loadModel("AdministratorModel", $_SESSION["ADMINISTRATOR"]);
		$_SERVER["ATTRIBUTES"]["ADMINISTRATOR"] = $administrator;
	}
}
?>
