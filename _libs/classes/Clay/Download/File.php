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
 * ファイルをダウンロードするためのヘルパクラスです。
 *
 * @package Download
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Download_File{
	protected $contentType;
	
	protected $file;
	
	/**
	 * ダウンロードファイルを初期化します。
	 */
	public function __construct($contentType = "text/plain"){
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		// コンテントタイプを設定
		$this->contentType = $contentType;
		
		// キャッシュ用に一時ファイルを作成
		$this->file = tmpfile();
	}
	
	/**
	 * ダウンロードファイルにデータを追加する。
	 */
	public function write($data){
		fwrite($this->file, $data);
	}
	
	/**
	 * ダウンロードの処理を実行して終了する。
	 */
	public function execute($filename, $encode = ""){
		// ファイルポインタを先頭に巻き戻す
		rewind($this->file);
		
		// ダウンロード用のヘッダを送信
		header("Content-Type: ".$this->contentType);
		header("Content-Disposition: attachment; filename=\"".$filename."\"");
		
		// ダウンロードの際は、よけいなバッファリングをクリア
		while(ob_get_level() > 0){
			ob_end_clean();
		}
				
		if(!empty($encode)){
			// エンコードを指定した場合は行単位で処理する。
			while(($buffer = fgets($this->file)) !== FALSE){
				echo mb_convert_encoding($buffer, $encode, "UTF-8");
			}
		}else{
			// エンコードが指定されない場合はバイト単位で処理する。
			while (!feof($this->file)) {
				echo fread($this->file, 65536);
			}
		}
		fclose($this->file);
		exit;
	}
}
 