<?php
/**
 * ### File.Csv.Upload
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
class File_Csv_Upload extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("File");

		if($params->check("key") && isset($_POST[$params->get("mode", "upload")])){
			// CSV設定を取得
			$csv = $loader->loadModel("CsvModel");
			$csv->findByCsvCode($params->get("key"));
			
			if(!empty($csv->csv_id)){			
				// CSVコンテンツ設定を取得
				$csvContent = $loader->loadModel("CsvContentModel");
				$csvContents = $csvContent->getCotentArrayByCsv($csv->csv_id);
				
				// アップロードファイルが正常にアップされた場合
				if($_FILES[$params->get("key")]["error"] == 0){
					// アップロードファイルを開く
					if(($orgFp = fopen($_FILES[$params->get("key")]["tmp_name"], "r")) !== FALSE){
						// SJISのCSVファイルをUTF8に変換
						$fp = tmpfile();
						while (($buffer = fgets($orgFp)) !== false){
							fwrite($fp, mb_convert_encoding($buffer, "UTF-8", "Shift_JIS"));
						}
						rewind($fp);
						
						// ヘッダ行をスキップ
						for($i = 0; $i < $params->get("skip", 0); $i ++){
							fgetcsv($fp);
						}
						
						// CSVデータを読み込む
						while(($data = fgetcsv($fp)) !== FALSE){
							$saveData = array();
							foreach($csvContents as $content){
								$saveData[$content->content_key] = $data[$content->order - 1];
							}
							if(!is_array($_SERVER["ATTRIBUTES"][$csv->list_key])){
								$_SERVER["ATTRIBUTES"][$csv->list_key] = array();
							}
							$_SERVER["ATTRIBUTES"][$csv->list_key][] = $saveData;
						}
					}
				}
			}
		}
	}
}
?>
