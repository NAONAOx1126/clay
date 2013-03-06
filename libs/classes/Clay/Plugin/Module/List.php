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
 * 一覧取得用のモジュールクラスになります。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Plugin_Module_List extends Clay_Plugin_Module{
	protected function executeImpl($params, $type, $name, $result, $defaultSortKey = "create_time"){
		// サイトデータを取得する。
		$loader = new Clay_Plugin($type);
		$model = $loader->loadModel($name);
		
		// カテゴリが選択された場合、カテゴリの商品IDのリストを使う
		$conditions = array();
		if(is_array($_POST["search"])){
			foreach($_POST["search"] as $key => $value){
				if(!empty($value)){
					if($params->get("mode", "list") != "select" || !$params->check("select") || $key != substr($params->get("select"), 0, strpos($params->get("select"), "|"))){
						$conditions[$key] = $value;
					}
				}
			}
		}
		
		if(is_array($_SERVER["FILE_CSV_DOWNLOAD"]) && $_SERVER["FILE_CSV_DOWNLOAD"]["LIMIT"] > 0){
			$model->limit($_SERVER["FILE_CSV_DOWNLOAD"]["LIMIT"], $_SERVER["FILE_CSV_DOWNLOAD"]["OFFSET"]);
			$_SERVER["FILE_CSV_DOWNLOAD"]["OFFSET"] += $_SERVER["FILE_CSV_DOWNLOAD"]["LIMIT"];
		}
		$models = $model->findAllBy($conditions);
		if($params->get("mode", "list") == "list"){
			$_SERVER["ATTRIBUTES"][$result] = $models;
		}elseif($params->get("mode", "list") == "select"){
			$_SERVER["ATTRIBUTES"][$result] = array();
			if($params->check("select")){
				list($select_key, $select_value) = explode("|", $params->get("select"));
				foreach($models as $model){
					$_SERVER["ATTRIBUTES"][$result][$model->$select_key] = $model->$select_value;
				}
			}
		}
	}
}
 