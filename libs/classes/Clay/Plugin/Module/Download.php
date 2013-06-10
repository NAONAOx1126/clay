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
 * 一覧ダウンロード用のモジュールクラスになります。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Plugin_Module_Download extends Clay_Plugin_Module_Page{
	private $groupBy = "";
	
	protected function setGroupBy($groupBy){
		$this->groupBy = $groupBy;
	}
	
	protected function executeImpl($params, $type, $name, $result, $defaultSortKey = "create_time"){
		if(!$params->check("search") || isset($_POST[$params->get("search")])){
			$_POST["page"] = 1;
			parent::executeImpl($params, $type, $name, $result, $defaultSortKey);
		
			// ヘッダを送信
			header("Content-Type: application/csv");
			header("Content-Disposition: attachment; filename=\"".$params->get("prefix", "csvfile").date("YmdHis").".csv\"");
			
			$titles = explode(",", $params->get("titles"));
			$columns = explode(",", $params->get("columns"));
			
			// CSVヘッダを出力
			echo "\"".implode("\",\"", $titles)."\"\r\n";
			
			// データが０件以上の場合は繰り返し
			while(count($_SERVER["ATTRIBUTES"][$result]) > 0){
				foreach($_SERVER["ATTRIBUTES"][$result] as $data){
					foreach($columns as $index => $column){
						if($index > 0) echo ",";
						eval('echo "\"".$data->'.$column.'."\""');
					}
					echo "\r\n";
				}
				$_POST["page"] ++;
				parent::executeImpl($params, $type, $name, $result, $defaultSortKey);
			}
		}
	}
}
 