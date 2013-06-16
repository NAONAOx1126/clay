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
	
	// POSTにINPUT=NEWが渡った場合は、入力をクリアする。
	if(isset($_POST["INPUT"]) && $_POST["INPUT"] == "NEW"){
		unset($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY]);
	}
		
	// INPUT_DATAのセッションの内容をPOSTに戻す。（POST優先）
	print_r($_POST);
	if(is_array($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY])){
		foreach($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY] as $key => $value){
			if(!isset($_POST[$key])){
				$_POST[$key] = $value;
			}
		}
	}
	print_r($_POST);
	Clay_Logger::writeDebug("Page Session Started.");
}
?>