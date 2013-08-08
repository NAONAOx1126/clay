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
abstract class Clay_Plugin_Module_Download extends Clay_Plugin_Module{
	private $groupBy = "";
	
	protected function setGroupBy($groupBy){
		$this->groupBy = $groupBy;
	}
	
	protected function executeImpl($params, $type, $name, $result, $defaultSortKey = "create_time"){
		if(!$params->check("search") || isset($_POST[$params->get("search")])){
			$loader = new Clay_Plugin($type);
			$loader->LoadSetting();
			
			// カテゴリが選択された場合、カテゴリの商品IDのリストを使う
			$conditions = array();
			if(is_array($_POST["search"])){
				foreach($_POST["search"] as $key => $value){
					if(!empty($value)){
						$conditions[$key] = $value;
					}
				}
			}
				
			// 並べ替え順序が指定されている場合に適用
			$sortOrder = "";
			$sortReverse = false;
			if($params->check("sort_key")){
				$sortOrder = $_POST[$params->get("sort_key")];
				if(empty($sortOrder)){
					$sortOrder = $defaultSortKey;
					$sortReverse = true;
				}elseif(preg_match("/^rev@/", $sortOrder) > 0){
					list($dummy, $sortOrder) = explode("@", $sortOrder);
					$sortReverse = true;
				}
			}
			
			$model = $loader->LoadModel($name);
				
			// 顧客データを検索する。
			if($this->groupBy){
				$model->setGroupBy($this->groupBy);
			}
			$result = $model->findAllBy($conditions, $sortOrder, $sortReverse);

			$titles = explode(",", $params->get("titles"));
			$columns = explode(",", $params->get("columns"));

			// ヘッダを送信
			header("Content-Type: application/csv");
			header("Content-Disposition: attachment; filename=\"".$params->get("prefix", "csvfile").date("YmdHis").".csv\"");
			
			ob_end_clean();

			// CSVヘッダを出力
			echo mb_convert_encoding("\"".implode("\",\"", $titles)."\"\r\n", "Shift_JIS", "UTF-8");
				
			while($data = $result->fetch()){
				
				// データが０件以上の場合は繰り返し
				foreach($columns as $index => $column){
					if($index > 0) echo ",";
					eval('echo "\"".mb_convert_encoding($data["'.$column.'"], "Shift_JIS", "UTF-8")."\"";');
				}
				echo "\r\n";
			}
			exit;
		}
	}
}
 