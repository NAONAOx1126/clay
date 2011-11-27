<?php
/**
 * Smarty plugin
 *
 * This plugin is only for Smarty2 BC
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {redirect} function plugin
 *
 * Type:     function<br>
 * Name:     redirect<br>
 * Purpose:  redirect page module.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_redirect($params, $smarty, $template){
	if(!empty($params["url"]) && !empty($_POST)){
		header("Location: ".$params["url"]);
		exit;
	}
}
?>