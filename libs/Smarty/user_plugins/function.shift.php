<?php
/**
 * Smarty plugin
 *
 * This plugin is only for Smarty2 BC
 * @package Smarty
 * @subpackage PluginsFunction
 */

/**
 * Smarty {shift} function plugin
 *
 * Type:     function<br>
 * Name:     shift<br>
 * Purpose:  shift page module.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_shift($params, $smarty, $template){
	if(!empty($params["path"]) && !empty($_POST)){
		// 遷移時に既に出力したバッファをクリアする。
		ob_end_clean();
		ob_start();
		// 別のテンプレートに対してdisplayを呼び出す。
		$_SERVER["TEMPLATE"]->display($params["path"]);
		exit;
	}
}
?>