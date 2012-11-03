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
 * 入力チェックエラー用の例外クラスです。
 * システム上でエラーメッセージをリスト化して保持することができ、
 * モジュール内で処理されなかった場合は、次のモジュールに引き継いで処理させることができます。
 *
 * @package Exception
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Exception_Invalid extends Clay_Exception_System{
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
 