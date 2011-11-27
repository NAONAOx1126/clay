<?php
/**
 * ### Base.Operator.Logout
 * 管理画面のログアウト処理を実行する。
 */
class Base_Operator_Logout extends FrameworkModule{
	function execute($params){
		unset($_SESSION["OPERATOR"]);
	}
}
?>
