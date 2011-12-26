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
class File_Csv_Download extends FrameworkModule{
	function execute($params){
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		// ローダーを初期化
		$loader = new PluginLoader("File");

		if($params->check("key")){
			// CSV設定を取得
			$csv = $loader->loadModel("CsvModel");
			$csv->findByCsvCode($params->get("key"));
			
			if(!empty($csv->csv_id)){			
				// CSVコンテンツ設定を取得
				$csvContent = $loader->loadModel("CsvContentModel");
				$csvContents = $csvContent->getCotentArrayByCsv($csv->csv_id);

				// ダウンロードの際は、よけいなバッファリングをクリア
				ob_end_clean();
				
				$_SERVER["FILE_CSV_DOWNLOAD"]["OFFSET"] = 0;
				$_SERVER["FILE_CSV_DOWNLOAD"]["LIMIT"] = $params->get("unit", PHP_INT_MAX);
				$_SERVER["FILE_CSV_DOWNLOAD"]["CSV"] = $csv;
				$_SERVER["FILE_CSV_DOWNLOAD"]["CSV_CONTENTS"] = $csvContents;
				
				header("Content-Type: application/csv");
				header("Content-Disposition: attachment; filename=\"".$csv->csv_code.date("YmdHis").".csv\"");

				// リストコンテンツをループさせる。
				$_SERVER["ATTRIBUTES"][$csv->list_key] = array();
				foreach($csvContents as $csvContent){
					$_SERVER["ATTRIBUTES"][$csv->list_key][] = $csvContent->column_name;
				}
				echo mb_convert_encoding("\"".implode("\",\"", $_SERVER["ATTRIBUTES"][$csv->list_key])."\"\r\n", "Shift_JIS", "UTF-8");
				ob_start();
			}
		}
	}
}
?>
