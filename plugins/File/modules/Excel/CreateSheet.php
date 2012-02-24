<?php
/**
 * ### File.Excel.CreateSheet
 * Excelのシートを追加するためのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   File
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param sheet Excelファイルに追加するシート名
 * @param titles シートの一覧のタイトル
 * @param columns シートのカラム名
 * @param key シートのデータとして使用する変数キー名
 */
class File_Excel_CreateSheet extends FrameworkModule{
	function execute($params){
		// データ一括取得のため、処理期限を無効化
		ini_set("max_execution_time", 0);
		
		if(!isset($_SERVER["PHP_EXCEL"])){
			require(FRAMEWORK_CLASS_LIBRARY_HOME."/PHPExcel.php");
			$_SERVER["PHP_EXCEL"] = new PHPExcel();
			$_SERVER["PHP_EXCEL"]->setActiveSheetIndex(0);
		}else{
			$newIndex = $_SERVER["PHP_EXCEL"]->getSheetCount();
			$_SERVER["PHP_EXCEL"]->createSheet($newIndex);
			$_SERVER["PHP_EXCEL"]->setActiveSheetIndex($newIndex);
		}
		if($params->check("key") && $params->check("titles") && $params->check("columns")){
			$list = $_SERVER["ATTRIBUTES"][$params->get("key")];
			$titles = explode(",", $params->get("titles"));
			$columns = explode(",", $params->get("columns"));
			
			$source = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			foreach($titles as $index => $title){
				$_SERVER["PHP_EXCEL"]->getActiveSheet()->getDefaultStyle()->getFont()->setName(mb_convert_encoding('ＭＳ Ｐ ゴシック','cp932'));
				$_SERVER["PHP_EXCEL"]->getActiveSheet()->getDefaultStyle()->getFont()->setSize(11); 
				$_SERVER["PHP_EXCEL"]->getActiveSheet()->setCellValue(substr($source, $index, 1)."1", $title);
				$cellBorders = $_SERVER["PHP_EXCEL"]->getActiveSheet()->getStyleByColumnAndRow($index, 1)->getBorders();
				$cellBorders->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
				if($index == 0){
					$cellBorders->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
				}else{
					$cellBorders->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
				}
				$cellBorders->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
				if($index == count($titles) - 1){
					$cellBorders->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
				}else{
					$cellBorders->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
				}
    		}
			foreach($list as $line => $data){
				foreach($columns as $index => $column){
					if(is_array($data)){
						$_SERVER["PHP_EXCEL"]->getActiveSheet()->setCellValue(substr($source, $index, 1).($line + 2), $data[$column]);
					}else{
						$_SERVER["PHP_EXCEL"]->getActiveSheet()->setCellValue(substr($source, $index, 1).($line + 2), $data->$column);
					}
					$cellBorders = $_SERVER["PHP_EXCEL"]->getActiveSheet()->getStyleByColumnAndRow($index, $line + 2)->getBorders();
					$cellBorders->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					if($index == 0){
						$cellBorders->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
					}else{
						$cellBorders->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					}
					if($line == count($list) - 1){
						$cellBorders->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
					}else{
						$cellBorders->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);	
					}
					if($index == count($titles) - 1){
						$cellBorders->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
					}else{
						$cellBorders->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
					}
				}			
			}
		}
		$skip = $params->get("skip", 0);
		foreach($columns as $index => $column){
			if($index < $skip){
				if($index == 0){
					$_SERVER["PHP_EXCEL"]->getActiveSheet()->mergeCells("A".($line + 2).":".substr($source, $skip - 1, 1).($line + 2));
					$_SERVER["PHP_EXCEL"]->getActiveSheet()->setCellValue("A".($line + 2), "合計");
				}
			}else{
				$colText = substr($source, $index, 1);
				$_SERVER["PHP_EXCEL"]->getActiveSheet()->setCellValue($colText.($line + 2), "=SUM(".$colText."2:".$colText.($line + 1).")");
			}
			$cellBorders = $_SERVER["PHP_EXCEL"]->getActiveSheet()->getStyleByColumnAndRow($index, $line + 2)->getBorders();
			$cellBorders->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
			if($index == 0){
				$cellBorders->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
			}else{
				$cellBorders->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
			}
			$cellBorders->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
			if($index == count($titles) - 1){
				$cellBorders->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);
			}else{
				$cellBorders->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOTTED);
			}
		}			
		if($params->check("sheet")){
			$_SERVER["PHP_EXCEL"]->getActiveSheet()->setTitle($params->get("sheet", "test"));
		}
	}
}
?>
