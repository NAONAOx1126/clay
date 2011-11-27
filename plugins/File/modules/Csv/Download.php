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
				
				header("Content-Type: application/csv");
				header("Content-Disposition: attachment; filename=\"".$csv->csv_code.date("YmdHis").".csv\"");

				// リストコンテンツを取得
				$list = $_SERVER["ATTRIBUTES"][$csv->list_key];
				
				// リストコンテンツをループさせる。
				$row = array();
				foreach($csvContents as $csvContent){
					$row[] = $csvContent->column_name;
				}
				echo mb_convert_encoding("\"".implode("\",\"", $row)."\"\r\n", "Shift_JIS", "UTF-8");						
				foreach($list as $item){
					$row = array();
					foreach($csvContents as $csvContent){
						$contentKeys = explode(".", $csvContent->content_key);
						$text = $item;
						foreach($contentKeys as $key){
							if(is_array($text)){
								$text = $text[$key];
							}elseif(is_object($text)){
								$text = $text->$key;
							}
						}
						$row[] = $text;
					}
					echo mb_convert_encoding("\"".implode("\",\"", $row)."\"\r\n", "Shift_JIS", "UTF-8");						
				}
				exit;
			}
		}
	}
}
?>
