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
 * ロケール設定用の起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_Locale{
	public static function start(){
		// デフォルトのロケールを設定する。
		if($_SERVER["CONFIGURE"]->LOCALE != ""){
			setlocale(LC_ALL, $_SERVER["CONFIGURE"]->LOCALE); 
		}
	}
}
