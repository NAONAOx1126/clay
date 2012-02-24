<?php
/**
 * ### File.Excel.Download
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
class File_Excel_Download extends FrameworkModule{
	function execute($params){
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		if(isset($_SERVER["PHP_EXCEL"])){
			ob_end_clean();
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="'.$params->get("file").'.xls"');
			header('Cache-Control: max-age=0');
			
			$objWriter = PHPExcel_IOFactory::createWriter($_SERVER["PHP_EXCEL"], 'Excel5');
			$objWriter->save('php://output');
			unset($_SERVER["PHP_EXCEL"]);
			ob_start();
		}
		exit;
	}
}
?>
