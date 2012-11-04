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
 * セッションIDをGETの値から取得するための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_SessionId{
	public static function start(){
		// 引数にセッションIDが指定された場合、セッションIDを上書き
		if(!empty($_GET[session_name()])){
			session_id($_GET[session_name()]);
			unset($_GET[session_name()]);
		}
	}
}
 