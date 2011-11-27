<?php
/**
 * ### Base.Operator.Login
 * 管理画面のログイン処理を実行する。
 */
class Base_Operator_Login extends FrameworkModule{
	function execute($params){
		if(empty($_SESSION["OPERATOR"])){
			// ログインIDのサイトコードを照合する。
			if($_SERVER["CONFIGURE"]->site_code != $_POST["login_id"]){
				throw new InvalidException(array("ログイン情報が正しくありません。"));
			}
			
			// 保存されたパスワードと一致するか調べる。
			if($_SERVER["CONFIGURE"]->site_password != $_POST["password"]){
				throw new InvalidException(array("ログイン情報が正しくありません。"));
			}
			
			// ログインに成功した場合には管理者情報をセッションに格納する。
			$_SESSION["OPERATOR"] = $_SERVER["CONFIGURE"];
		}else{
			// ログインIDのサイトコードを照合する。
			if($_SERVER["CONFIGURE"]->site_code != $_SESSION["OPERATOR"]->site_code){
				throw new InvalidException(array("ログイン情報が正しくありません。"));
			}
			
			// 保存されたパスワードと一致するか調べる。
			if($_SERVER["CONFIGURE"]->site_password != $_SESSION["OPERATOR"]->site_password){
				throw new InvalidException(array("ログイン情報が正しくありません。"));
			}
		}
	}
}
?>
