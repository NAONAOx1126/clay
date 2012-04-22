<?php
/**
 * ログを出力するためのヘルパークラスです。
 *
 * @category  Logger
 * @package   Common
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

/**
 * ログ出力用のクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class Logger{
	private static $startTime;
	
	/**
	 * メッセージログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	private static function writeMessage($prefix, $message, $exception = null){
		try{
			// ログディレクトリが無い場合は自動的に作成
			$logHome = FRAMEWORK_LOGS_HOME."/".$_SERVER["CONFIGURE"]->get("site_code");
			if(!is_dir($logHome)){
				mkdir($logHome);
				@chmod($logHome, 0777);
			}
			// ログファイルに記載
			$logFile = $logHome."/".$prefix.date("Ymd").".log";
			if(($fp = fopen($logFile, "a+")) !== FALSE){
				fwrite($fp, "[".$_SERVER["SERVER_NAME"]."][".date("Y-m-d H:i:s")."]".$message."\r\n");
				if($exception != null){
					fwrite($fp, "[".$_SERVER["SERVER_NAME"]."][".date("Y-m-d H:i:s")."]".$exception->getMessage()."\r\n");
					fwrite($fp, "[".$_SERVER["SERVER_NAME"]."][".date("Y-m-d H:i:s")."]".$exception->getTraceAsString());
				}
				fclose($fp);
				@chmod($logFile, 0666);
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
		Logger::writeMessage("error", $message, $exception);
	}
	
	/**
	 * 警告ログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeAlert($message){
		Logger::writeMessage("alert", $message);
	}
	
	/**
	 * 情報ログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeInfo($message){
		Logger::writeMessage("info", $message);
	}
	
	/**
	 * デバッグログを出力する。
	 *
	 * @params string $message エラーメッセージ
	 * @params Exception $exception エラーの原因となった例外オブジェクト
	 */
	public static function writeDebug($message){
		if($_SERVER["CONFIGURE"]->DEBUG){
			Logger::writeMessage("debug", $message);
		}
	}
}
?>