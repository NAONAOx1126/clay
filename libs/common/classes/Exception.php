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
 * システム共通例外のクラスです。
 *
 * @package Exception
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class SystemException extends Exception{
	/**
	 * エラーの元となった例外
	 */
	var $source;
	
	/**
	 * コンストラクタ
	 * @param $message 例外のメッセージ
	 * @param $code 例外のエラーコード
	 * @param $source 例外の原因となった例外
	 */
	public function __construct($message = "", $code = 0, $source = null){
		parent::__construct($message, $code);
		
		$this->source = $source;
	}
}

/**
 * 入力チェックエラー用の例外クラスです。
 * システム上でエラーメッセージをリスト化して保持することができ、
 * モジュール内で処理されなかった場合は、次のモジュールに引き継いで処理させることができます。
 *
 * @package Exception
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class InvalidException extends SystemException{
	/** 
	 * 入力のエラーメッセージリスト 
	 */
	private $errors;
	
	/**
	 * コンストラクタ
	 * @param $errors 入力エラーのメッセージリスト
	 * @param $code この例外のエラーコード
	 */
	public function __construct($errors, $code = 0){
		$this->errors = $errors;
		parent::__construct(implode("\r\n", $errors), $code);
	}
	
	/**
	 * 入力のエラーメッセージのリストを取得する。
	 * @return 入力エラーのメッセージリスト
	 */
	public function getErrors(){
		return $this->errors;
	}
}

/**
 * データベースエラー時の例外クラスです。
 *
 * @package Exception
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class DatabaseException extends SystemException{
	/**
	 * エラーオブジェクト 
	 */
	private $err;
	
	/**
	 * コンストラクタ
	 * @param $err この例外の原因となったデータベースの例外
	 * @param $code この例外のエラーコード
	 */
	public function __construct($err, $code = 0){
		$this->err = $err;
		parent::__construct($err->getMessage(), $code);
	}

	/**
	 * データベース例外を取得します。
	 * @return Exception この例外の原因となったデータベースの例外
	 */
	public function getError(){
		return $this->err;
	}
}
?>
