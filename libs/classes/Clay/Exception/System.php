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
 * システム共通例外のクラスです。
 *
 * @package Exception
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Exception_System extends Exception{
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
