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
 * ファイルをアップロードするためのヘルパクラスです。
 *
 * @package Upload
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Upload_File{
	protected $line;
	
	protected $code;
	
	protected $file;
	
	/**
	 * アップロードファイルを初期化します。
	 */
	public function __construct(){
		// 時間のかかる処理のため、処理期限を無効化
		ini_set("max_execution_time", 0);
	}
	
	/**
	 * アップロードしたファイルを保存する際のコードを生成する。
	 */
	public function getFileCode(){
		return $this->code;
	}
	
	/**
	 * アップロードしたファイルを保存する際のファイル名を生成する。
	 */
	public function getFileName(){
		return CLAY_ROOT.DIRECTORY_SEPARATOR."_uploads".DIRECTORY_SEPARATOR.$this->getFileCode();
	}
	
	/**
	 * アップロードファイルを取り込み、ローカルファイルとして処理できるようにします。
	 */
	public function initialize($key, $encode = ""){
		if($_FILES[$key]["error"] == 0){
			$this->code = uniqid($key);
			if(($fp = fopen($_FILES[$key]["tmp_name"], "r")) !== FALSE){
				// エラーで無い場合のみ、データを展開
				$filename = $this->getFileName();
				
				if(($fp2 = fopen($filename, "w+")) !== FALSE){
					if(!empty($encode)){
						// エンコードを指定した場合は行単位で処理する。
						while(($buffer = fgets($fp)) !== FALSE){
							$buffer = str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $buffer)));
							fwrite($fp2, mb_convert_encoding($buffer, "UTF-8", $encode));
						}
					}else{
						// エンコードが指定されない場合はバイト単位で処理する。
						while (!feof($this->file)) {
							fwrite($fp2, fread($this->file, 65536));
						}
					}
					fclose($fp2);
				}
				fclose($fp);
			}
			$this->file = fopen($filename, "r");
			$this->line = 0;
			return $filename;
		}else{
			throw new Clay_Exception_Invalid(array("アップロードファイルが正しくありません。"));
		}
		return "";
	}
	
	/**
	 * ファイルの設定を復元する。
	 */
	public function load($code){
		$this->code = $code;
		$this->file = fopen($this->getFileName(), "r");
		$this->line = 0;
	}
	
	/**
	 * アップロードファイルの全ての行データに処理を実行する。
	 */
	public function read($callback){
		while(($data = fgets($this->file)) !== FALSE){
			call_user_func($callback, $this->line ++, $data);
		}
	}
	
	/**
	 * ファイルを先頭位置に戻す。
	 */
	public function rewind(){
		fseek($this->file, 0);
		$this->line = 0;
	}
}
 