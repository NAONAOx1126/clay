<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */

/**
 * Smarty {end_session} function plugin
 *
 * Type:     function<br>
 * Name:     end_session<br>
 * Purpose:  end session module.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_end_session($params, $smarty, $template){
	// POSTの内容をセッションに戻す
	if(is_array($_POST)){
		$_SESSION["INPUT_DATA"] = array(TEMPLATE_DIRECTORY => array());
		foreach($_POST as $key => $value){
			$_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY][$key] = $value;
		}
	}
	foreach($_SERVER as $name =>$value){
		$_SERVER["TEMPLATE"]->assign($name, $value);
	}
	// 入力パラメータのアサイン処理
	$_SERVER["TEMPLATE"]->assign("INPUT", $_POST);
	// セッション名のアサイン処理
	$_SERVER["TEMPLATE"]->assign("PHPSessionName", session_name());
	// セッションIDのアサイン処理
	$_SERVER["TEMPLATE"]->assign("PHPSessionId", session_id());
	Clay_Logger::writeDebug("Page Session Ended.");
}
?>