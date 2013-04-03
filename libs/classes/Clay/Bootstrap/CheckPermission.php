<?php
/**
 * Copyright (C) 2012 Clay System All Rights Reserved.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Clay System
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
		// cacheに書き込み権限が必要です。
		if(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache")){
			mkdir(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache");
			chmod(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache", 0777);
		}
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."_cache"."\"に書き込み許可を与えてください。";
			exit;
		}elseif(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache".DIRECTORY_SEPARATOR.".htaccess")){
			if(($fp = fopen(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache".DIRECTORY_SEPARATOR.".htaccess", "w+")) !== FALSE){
				fwrite($fp, "Order allow,deny\r\n");
				fwrite($fp, "Deny from all\r\n");
				fclose($fp);
			}
		}
		// cache_smartyに書き込み権限が必要です。
		if(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty")){
			mkdir(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty");
			chmod(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty", 0777);
		}
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty"."\"に書き込み許可を与えてください。";
			exit;
		}elseif(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty".DIRECTORY_SEPARATOR.".htaccess")){
			if(($fp = fopen(CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty".DIRECTORY_SEPARATOR.".htaccess", "w+")) !== FALSE){
				fwrite($fp, "Order allow,deny\r\n");
				fwrite($fp, "Deny from all\r\n");
				fclose($fp);
			}
		}
		// contentsに書き込み権限が必要です。
		if(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_contents")){
			mkdir(CLAY_ROOT.DIRECTORY_SEPARATOR."_contents");
			chmod(CLAY_ROOT.DIRECTORY_SEPARATOR."_contents", 0777);
		}
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."_contents")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."_contents"."\"に書き込み許可を与えてください。";
			exit;
		}elseif(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_contents".DIRECTORY_SEPARATOR.".htaccess")){
			if(($fp = fopen(CLAY_ROOT.DIRECTORY_SEPARATOR."_contents".DIRECTORY_SEPARATOR.".htaccess", "w+")) !== FALSE){
				fwrite($fp, "DirectoryIndex index.php\r\n");
				fwrite($fp, "RewriteEngine off\r\n");
				fwrite($fp, "<Files ~ \"\.(html?|xml)$\">\r\n");
				fwrite($fp, "Order deny,allow\r\n");
				fwrite($fp, "Deny from all\r\n");
				fwrite($fp, "</Files>\r\n");
				fclose($fp);
			}
		}
		// logsに書き込み権限が必要です。
		if(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_logs")){
			mkdir(CLAY_ROOT.DIRECTORY_SEPARATOR."_logs");
			chmod(CLAY_ROOT.DIRECTORY_SEPARATOR."_logs", 0777);
		}
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."_logs")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."_logs"."\"に書き込み許可を与えてください。";
			exit;
		}elseif(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_logs".DIRECTORY_SEPARATOR.".htaccess")){
			if(($fp = fopen(CLAY_ROOT.DIRECTORY_SEPARATOR."_logs".DIRECTORY_SEPARATOR.".htaccess", "w+")) !== FALSE){
				fwrite($fp, "Order allow,deny\r\n");
				fwrite($fp, "Deny from all\r\n");
				fclose($fp);
			}
		}
		// uploadに書き込み権限が必要です。
		if(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads")){
			mkdir(CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads");
			chmod(CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads", 0777);
		}
		if(!is_writable(CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads")){
			echo "\"".CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads"."\"に書き込み許可を与えてください。";
			exit;
		}elseif(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads".DIRECTORY_SEPARATOR.".htaccess")){
			if(($fp = fopen(CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads".DIRECTORY_SEPARATOR.".htaccess", "w+")) !== FALSE){
				fwrite($fp, "Order allow,deny\r\n");
				fwrite($fp, "Deny from all\r\n");
				fclose($fp);
			}
		}
	}
}
 