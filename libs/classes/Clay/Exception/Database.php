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
 * データベースエラー時の例外クラスです。
 *
 * @package Exception
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Exception_Database extends Clay_Exception_System{
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
 