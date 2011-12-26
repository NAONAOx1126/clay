<?php
/**
 * ### File.Csv.UploadEnd
 * CSVアップロードを処理するためのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   File
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param mode 処理を実行するトリガーとなるPOSTキー
 * @param key ファイルのCSV形式を特定するためのキー
 * @param skip ヘッダとして読み込みをスキップする行数
 */
class File_Csv_UploadEnd extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("File");

		if(is_array($_SERVER["FILE_CSV_UPLOAD"])){
			$fp = $_SERVER["FILE_CSV_UPLOAD"]["FP"];
			$csv = $_SERVER["FILE_CSV_UPLOAD"]["CSV"];
			$csvContents = $_SERVER["FILE_CSV_UPLOAD"]["CSV_CONTENTS"];

			$i = 0;
			$_SERVER["ATTRIBUTES"][$csv->list_key] = null;
			while($i < $_SERVER["FILE_CSV_UPLOAD"]["LIMIT"] && ($data = fgetcsv($fp)) !== FALSE){
				$saveData = array();
				foreach($csvContents as $content){
					$saveData[$content->content_key] = $data[$content->order - 1];
				}
				if(!is_array($_SERVER["ATTRIBUTES"][$csv->list_key])){
					$_SERVER["ATTRIBUTES"][$csv->list_key] = array();
				}
				$_SERVER["ATTRIBUTES"][$csv->list_key][] = $saveData;
				$i ++;
			}
			if($_SERVER["ATTRIBUTES"][$csv->list_key] == null){
				fclose($fp);
				unset($_SERVER["FILE_CSV_UPLOAD"]);
			}
		}
	}
}
?>
