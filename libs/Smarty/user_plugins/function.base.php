<?php
/**
 * Smarty plugin
 *
 * This plugin is only for Smarty2 BC
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {base} function plugin
 *
 * Type:     function<br>
 * Name:     base<br>
 * Purpose:  output base url module.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_base($params, $smarty, $template){
	if(substr(FRAMEWORK_URL_BASE, -1) == "/"){
		return substr(FRAMEWORK_URL_BASE, 0, -1);
	}
	return FRAMEWORK_URL_BASE;
}
?>