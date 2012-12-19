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
 * PHPTAL用のプレフィルタ用クラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Clay_Template_Phptal_PreFilter implements PHPTAL_Filter{
	private $filters = array();
	
	public function add(PHPTAL_Filter $filter){
		$this->filters[] = $filter;
	}
	
	public function filter($source){
		// セッションをスタートし、とりあえず成功のヘッダを送信する
		session_start();
		header("HTTP/1.1 200 OK");
		
		// POSTにINPUT=NEWが渡った場合は、入力をクリアする。
		if(isset($_POST["INPUT"]) && $_POST["INPUT"] == "NEW"){
			unset($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY]);
		}
		
		// INPUT_DATAのセッションの内容をPOSTに戻す。（POST優先）
		if(is_array($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY])){
			foreach($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY] as $key => $value){
				if(!isset($_POST[$key])){
					$_POST[$key] = $value;
				}
			}
		}
		Clay_Logger::writeDebug("Page Session Started.");
		
		// フィルタの処理を実行
		foreach($this->filters as $filter){
			$source = $filter->filter($source);
		}
		
		// POSTの入力をセッションに戻す
		if(is_array($_POST)){
			$_SESSION["INPUT_DATA"] = array(TEMPLATE_DIRECTORY => array());
			foreach($_POST as $key => $value){
				$_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY][$key] = $value;
			}
		}
		
		// Server変数をテンプレートにアサインする。
		foreach($_SERVER as $name =>$value){
			$_SERVER["TEMPLATE"]->assign($name, $value);
		}
		// 入力データをテンプレートにアサインする。
		$_SERVER["TEMPLATE"]->assign("INPUT", $_POST);
		
		Clay_Logger::writeDebug("Page Session Ended.");
		
		return $source;
	}
}