<?php
/**
 * Smarty plugin
 *
 * This plugin is only for Smarty2 BC
 * @package Smarty
 * @subpackage PluginsFunction
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
	$_SERVER["TEMPLATE"]->assign("INPUT", $_POST);
	Logger::writeDebug("Page Session Ended.");
}
?>