<?php
/**
 * Smarty {loadmodule} function plugin
 *
 * Type:     function<br>
 * Name:     loadmodule<br>
 * Purpose:  load metamorfosi module function.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
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
	$errors = array();
	try{
		// モジュール用のクラスをリフレクション
		$object = $_SERVER["LOADER"]->loadModule($name);
		Logger::writeDebug($name." = ".var_export($object, true));
		if(method_exists($object, "execute")){
			$object->execute(new LoadModuleParams($params));
		}else{
			Logger::writeAlert($name." is not plugin module.");
		}
	}catch(InvalidException $e){
		// 入力エラーなどの例外（ただし、メッセージリストを空にすると例外処理を行わない）
		Logger::writeError($e->getMessage(), $e);
		$errors = $e->getErrors();
	}catch(SystemException $e){
		// システムエラーの例外処理
		Logger::writeError($e->getMessage(), $e);
		$errors = array($e->getMessage());
	}catch(ErrorException $e){
		// システムエラーの例外処理
		Logger::writeError($e->getMessage(), $e);
		$errors = array($e->getMessage());
	}
	
	// エラー配列をスタックさせる
	if(!empty($errors)){
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