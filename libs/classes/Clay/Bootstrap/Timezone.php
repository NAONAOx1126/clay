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
 * PHP5.3以降のタイムゾーン設定エラー対策の起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_Timezone{
	public static function start(){
		// デフォルトのタイムゾーンを設定する。
		if($_SERVER["CONFIGURE"]->TIMEZONE != ""){
			date_default_timezone_set($_SERVER["CONFIGURE"]->TIMEZONE);
		}
	}
}
