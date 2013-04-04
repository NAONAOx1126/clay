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
class Clay_Upload_Csv extends Clay_Upload_File{
	/**
	 * ダウンロードファイルを初期化します。
	 */
	public function __construct(){
		parent::__construct();
	}
	
	/**
	 * アップロードファイルを取り込み、ローカルファイルとして処理できるようにします。
	 */
	public function initialize($key, $encode = ""){
		return parent::initialize($key, $encode).".csv";
	}
	
	/**
	 * アップロードファイルの全ての行データに処理を実行する。
	 */
	public function read($callback){
		while(($data = fgetcsv($this->file)) !== FALSE){
			call_user_func($callback, $this->line ++, $data);
		}
	}
}
 