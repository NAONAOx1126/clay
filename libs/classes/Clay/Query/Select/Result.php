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
 * データベースSELECTの結果処理用のクラスです。
 *
 * @package Query
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Query_Select_Result{
	/**
	 * クエリ実行に使ったプPrepared Statementオブジェクト
	 */
	private $result;

	/**
	 * データベースの参照結果を初期化します。
	 *
	 * @params object $prepare クエリ実行に使ったPrepared Statementオブジェクト
	 * @params object $result クエリの実行結果オブジェクト
	 */

	public function __construct($result){
		$this->result =& $result;
	}

	/**
	 * 次の実行結果レコードの連想配列を取得するメソッド
	 *
	 * @return array 次の実行結果レコードの連想配列、次のレコードが無い場合はFALSE
	 */
	public function next(){
		return $this->result->fetch();
	}

	/**
	 * 次の実行結果レコードの連想配列を取得するメソッド
	 *
	 * @return array 次の実行結果レコードの連想配列、次のレコードが無い場合はFALSE
	 */
	public function all(){
		return $this->result->fetchAll();
	}

	/**
	 * クエリの実行結果をクローズし、リソースを解放する
	 */
	public function close(){
		$this->result->close();
	}
}
 