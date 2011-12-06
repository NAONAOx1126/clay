<?php
/**
 * ### Base.Operator.Logout
 * 管理画面のログアウト処理を実行する。
 * 
 * @category  Modules
 * @package   Operator
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
class Base_Operator_Logout extends FrameworkModule{
	function execute($params){
		unset($_SESSION["OPERATOR"]);
	}
}
?>
