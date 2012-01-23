<?php
/**
 * ### File.Csv.Download
 * ファイルのダウンロードを行うためのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   File
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param key ファイルのCSV形式を特定するためのキー
 */
class File_Log_Newest extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("File");

		// CSV設定を取得
		$uploadLog = $loader->loadModel("UploadLogModel");
		$uploadLog->limit(1, 0);
		$uploadLogs = $uploadLog->findAllBy(array(), "upload_time", true);
		
		// 結果を設定
		if(is_array($uploadLogs) && !empty($uploadLogs)){
			$_SERVER["ATTRIBUTES"]["UPLOAD_LOG_NEWEST"] = $uploadLogs[0];
		}else{
			$_SERVER["ATTRIBUTES"]["UPLOAD_LOG_NEWEST"] = null;
		}
	}
}
?>
