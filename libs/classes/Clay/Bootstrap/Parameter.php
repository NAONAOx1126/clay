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
 * パラメータの値を調整するための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_Parameter{
	public static function start(){
		// HTTPのパラメータを統合する。（POST優先）
		foreach($_POST as $name => $value){
			if($_SERVER["CLIENT_DEVICE"]->isFuturePhone()){
				if(is_array($value)){
					foreach($value as $k => $v){
						$_GET[mb_convert_encoding($name, "UTF-8", "Shift_JIS")][mb_convert_encoding($k, "UTF-8", "Shift_JIS")] = mb_convert_encoding($v, "UTF-8", "Shift_JIS");
					}
				}else{
					$_GET[mb_convert_encoding($name, "UTF-8", "Shift_JIS")] = mb_convert_encoding($value, "UTF-8", "Shift_JIS");
				}
			}else{
				$_GET[$name] = $value;
			}
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
		$_POST = $_GET = Clay_Bootstrap_Parameter::removeMagicQuote($_GET);
	}

	// マジッククオートを解除する関数
	protected static function removeMagicQuote($value){
		if(get_magic_quotes_gpc() == "1"){
			if(is_array($value)){
				foreach($value as $i => $val){
					$value[$i] = remove_magic_quote($val);
				}
			}else{
				$value = str_replace("\\\"", "\"", $value);
				$value = str_replace("\\\'", "\'", $value);
				$value = str_replace("\\\\", "\\", $value);
			}
		}
		return $value;
	}
}
 