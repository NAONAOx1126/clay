<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * エラーメッセージの表示制御を行うための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_ErrorMessage{
	public static function start(){
		// エラーメッセージを限定させる。
		if(!isset($_SERVER["CONFIGURE"]->DISPLAY_ERROR)){
			$_SERVER["CONFIGURE"]->DISPLAY_ERROR = "Off";
		}
		if($_SERVER["CONFIGURE"]->DEBUG){
			if(defined("E_DEPRECATED")){
				error_reporting(E_ALL & ~ E_STRICT & ~ E_STRICT);
			}else{
				error_reporting(E_ALL & ~ E_STRICT);
			}
			ini_set('display_errors', $_SERVER["CONFIGURE"]->DISPLAY_ERROR);
			ini_set('log_errors', 'On');
		}else{
			error_reporting(E_ERROR);
			ini_set('display_errors', $_SERVER["CONFIGURE"]->DISPLAY_ERROR);
			ini_set('log_errors', 'On');
		}
	}
}
