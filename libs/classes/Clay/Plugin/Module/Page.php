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
 * ページング一覧表示用のモジュールです。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Plugin_Module_Page extends Clay_Plugin_Module{
	protected function executeImpl($params, $type, $name, $result, $defaultSortKey = "create_time"){
		$loader = new Clay_Plugin($type);
		$loader->LoadSetting();

		// ページャの初期化
		$pagerMode = $params->get("_pager_mode", Clay_Pager::PAGE_SLIDE);
		$pagerDisplay = $params->get("_pager_dispmode", Clay_Pager::DISPLAY_ATTR);
		if($params->check("_pager_per_page_key")){
			$pagerCount = $_POST[$params->get("_pager_per_page_key")];
		}else{
			$pagerCount = $params->get("_pager_per_page", 20);
		}
		if($params->check("_pager_displays_key")){
			$pagerNumbers = $_POST[$params->get("_pager_displays_key")];
		}else{
			$pagerNumbers = $params->get("_pager_displays", 3);
		}
		$pager = new Clay_Pager($pagerMode, $pagerDisplay, $pagerCount, $pagerNumbers);
		$pager->importTemplates($params);
		
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
		
		// 顧客データを検索する。
		$model = $loader->LoadModel($name);
		$pager->setDataSize($model->countBy($conditions));
		$model->limit($pager->getPageSize(), $pager->getCurrentFirstOffset());
		$models = $model->findAllBy($conditions, $sortOrder, $sortReverse);
		
		$_SERVER["ATTRIBUTES"][$result."_pager"] = $pager;
		$_SERVER["ATTRIBUTES"][$result] = $models;
	}
}
 