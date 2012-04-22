<?php
/**
 * Smarty plugin
 *
 * This plugin is only for Smarty2 BC
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {start_session} function plugin
 *
 * Type:     function<br>
 * Name:     start_session<br>
 * Purpose:  start session module.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_start_session($params, $smarty, $template){
	// セッションをスタートし、とりあえず成功のヘッダを送信する
	session_start();
	header("HTTP/1.1 200 OK");
	
	// INPUT_DATAのセッションの内容をPOSTに戻す。（POST優先）
	if(is_array($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY])){
		foreach($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY] as $key => $value){
			if(!isset($_POST[$key])){
				$_POST[$key] = $value;
			}
		}
	}
	$_SERVER["POST"] = $_POST;
	Logger::writeDebug("Page Session Started.");
}
?>