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
 * パラメータの値を調整するための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_Parameter{
	public static function start(){
		// HTTPのパラメータを統合する。（POST優先）
		foreach($_POST as $name => $value){
			$_GET[$name] = $value;
		}
		
		// input-imageによって渡されたパラメータを展開
		$inputImageKeys = array();
		foreach($_GET as $name => $value){
			if(preg_match("/^(.+)_([xy])$/", $name, $params) > 0){
				$inputImageKeys[$params[1]][$params[2]] = $value;
			}
		}
		foreach($inputImageKeys as $key => $inputImage){
			if(isset($inputImage["x"]) && isset($inputImage["y"])){
				$_GET[$key] = $inputImage["x"].",".$inputImage["y"];
				unset($_GET[$key."_x"]);
				unset($_GET[$key."_y"]);
			}
		}
		$_POST = $_GET;
	}
}
 