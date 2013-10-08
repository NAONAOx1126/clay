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
 * ログ出力用のクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Logger{
	/**
	 * メッセージログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	private static function writeMessage($prefix, $message, $exception = null){
		try{
			if(isset($_SERVER["CONFIGURE"]->site_code)){
				if(!empty($_SERVER["CONFIGURE"]->site_code)){
					$siteCode = $_SERVER["CONFIGURE"]->site_code;
				}else{
					$siteCode = "default";
				}
				// ログディレクトリが無い場合は自動的に作成
				$logHome = CLAY_ROOT.DIRECTORY_SEPARATOR."_logs".DIRECTORY_SEPARATOR;
				if(!is_dir($logHome)){
					mkdir($logHome);
					@chmod($logHome, 0777);
				}
				// 現在のログファイルが10MB以上の場合ローテーションする。
				if(file_exists($logHome.$siteCode.".log") && filesize($logHome.$siteCode.".log") > 1024 * 1024 * 10){
					if(defined("MAX_LOG_HISTORY")){
						$logHistorys = MAX_LOG_HISTORY;
					}else{
						$logHistorys = 10;
					}
					for($index = $logHistorys - 1; $index > 0; $index --){
						if(file_exists($logHome.$siteCode."_".$index.".log")){
							rename($logHome.$siteCode."_".$index.".log", $logHome.$siteCode."_".($index + 1).".log");
						}
					}
					rename($logHome.$siteCode.".log", $logHome.$siteCode."_1.log");
				}
				
				// ログファイルに記載
				$logFile = $logHome.$siteCode.".log";
				if(($fp = fopen($logFile, "a+")) !== FALSE){
					fwrite($fp, "[".$_SERVER["SERVER_NAME"]."][".date("Y-m-d H:i:s")."][".$prefix."]".$message."\r\n");
					if($exception != null){
						fwrite($fp, "[".$_SERVER["SERVER_NAME"]."][".date("Y-m-d H:i:s")."][".$prefix."]".$exception->getMessage()."\r\n");
						fwrite($fp, "[".$_SERVER["SERVER_NAME"]."][".date("Y-m-d H:i:s")."][".$prefix."]".$exception->getTraceAsString());
					}
					fclose($fp);
					@chmod($logFile, 0666);
				}
			}
		}catch(Exception $e){
			// エラーログ出力に失敗した場合は無限ネストの可能性があるため、例外を無効にする。
		}
	}
	
	/**
	 * エラーログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeError($message, $exception = null){
		Clay_Logger::writeMessage("error", $message, $exception);
	}
	
	/**
	 * 警告ログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeAlert($message){
		Clay_Logger::writeMessage("alert", $message);
	}
	
	/**
	 * 情報ログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeInfo($message){
		Clay_Logger::writeMessage("info", $message);
	}
	
	/**
	 * デバッグログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeDebug($message){
		if($_SERVER["CONFIGURE"]->DEBUG){
			Clay_Logger::writeMessage("debug", $message);
		}
	}
}
 