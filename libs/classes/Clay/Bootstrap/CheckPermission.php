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
 * パーミッションが正しく設定されているかチェックするための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_CheckPermission{
	public static function start(){
		// ホームに書き込み権限が必要です。
		if(!is_writable(CLAY_ROOT)){
			echo "\"".CLAY_ROOT."\"に書き込み許可を与えてください。";
			exit;
		}
		// configureに書き込み権限が必要です。
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."configure")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."configure"."\"に書き込み許可を与えてください。";
			exit;
		}
		// cacheに書き込み権限が必要です。
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."cache")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."cache"."\"に書き込み許可を与えてください。";
			exit;
		}
		// contentsに書き込み権限が必要です。
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."contents")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."contents"."\"に書き込み許可を与えてください。";
			exit;
		}
		// logsに書き込み権限が必要です。
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."logs")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."logs"."\"に書き込み許可を与えてください。";
			exit;
		}
	}
}
 