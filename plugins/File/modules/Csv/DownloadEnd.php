<?php
/**
 * ### File.Csv.DownloadEnd
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
class File_Csv_DownloadEnd extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("File");

		// CSV設定を取得
		$csv = $_SERVER["FILE_CSV_DOWNLOAD"]["CSV"];
		$csvContents = $_SERVER["FILE_CSV_DOWNLOAD"]["CSV_CONTENTS"];
		
		ob_end_clean();
		
		// リストコンテンツを出力させる。
		foreach($_SERVER["ATTRIBUTES"][$csv->list_key] as $item){
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
		unset($_SERVER["FILE_CSV_DOWNLOAD"]);
		exit;
	}
}
?>
