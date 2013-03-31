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
class LoadModuleParams{
	var $params;
	function __construct($params){
		$this->params = $params;
	}
	
	function check($name){
		if(isset($this->params[$name])){
			return $this->params[$name];
		}
		return null;
	}
	
	function get($name, $default = ""){
		if(isset($this->params[$name]) && $this->params[$name] != null && $this->params[$name] != ""){
			return $this->params[$name];
		}
		return $default;
	}
}


/**
 * Smarty {loadmodule} function plugin
 *
 * Type:     function<br>
 * Name:     loadmodule<br>
 * Purpose:  load framework module.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_loadmodule($params, $smarty, $template)
{
    // nameパラメータは必須です。
    if (empty($params['name'])) {
        trigger_error("loadmodule: missing name parameter", E_USER_WARNING);
        return;
    }
	
	// パラメータを変数にコピー
    $name = $params['name'];
	
	// errorパラメータはエラー例外時に指定されたテンプレートに変更する。
	if(isset($params["error"])){
	    $error = $params['error'];
	}else{
		$error = "";
	}
	
    // モジュールのクラスが利用可能か調べる。
	$errors = null;
	try{
		// モジュール用のクラスをリフレクション
		$loader = new Clay_Plugin("");
		$object = $loader->loadModule($name);
		if(method_exists($object, "execute")){
			Clay_Logger::writeDebug("MODULE : ".$name." start");
			// 検索条件と並べ替えキー以外を無効化する。
			if(isset($params["clear"]) && $params["clear"] == "1"){
				if(isset($params["sort_key"]) && !empty($params["sort_key"])){
					$_POST = array("search" => $_POST["search"], $params["sort_key"] => $_POST[$params["sort_key"]]);
				}else{
					$_POST = array("search" => $_POST["search"]);
				}
			}
			if(!empty($params["key_prefix"])){
				$object->key_prefix = $params["key_prefix"];
			}else{
				$object->key_prefix = "";
			}
			if(!empty($params["continue"])){
				$object->continue = $params["continue"];
			}else{
				$object->continue = "";
			}
			$object->execute(new LoadModuleParams($params));
			Clay_Logger::writeDebug("MODULE : ".$name." end");
		}else{
			Clay_Logger::writeAlert($name." is not plugin module.");
		}
	}catch(Clay_Exception_Invalid $e){
		// 入力エラーなどの例外（ただし、メッセージリストを空にすると例外処理を行わない）
		Clay_Logger::writeError($e->getMessage(), $e);
		$errors = $e->getErrors();
	}catch(Clay_Exception_System $e){
		// システムエラーの例外処理
		Clay_Logger::writeError($e->getMessage(), $e);
		$errors = array($e->getMessage());
	}catch(Exception $e){
		// システムエラーの例外処理
		Clay_Logger::writeError($e->getMessage(), $e);
		$errors = array($e->getMessage());
	}
	
	// エラー配列をスタックさせる
	if($errors !== null){
		
		if(!empty($error)){
			// errorパラメータが渡っている場合はスタックさせたエラーを全て出力してエラー画面へ
			$_SERVER["TEMPLATE"]->assign("ERRORS", $errors);
			unset($_SERVER["ERRORS"]);
			$_SERVER["TEMPLATE"]->display($error);
			exit;
		}else{
			// エラー用配列が配列になっていない場合は初期化
			if(!is_array($_SERVER["ERRORS"])){
				$_SERVER["ERRORS"] = array();
			}
			
			// エラー内容をマージさせる。
			$_SERVER["ERRORS"] = array_merge($_SERVER["ERRORS"], $errors);
		}
	}
}
?>