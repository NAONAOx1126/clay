<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * 詳細表示用のモジュールです。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Plugin_Module_Detail extends Clay_Plugin_Module_List{
	public function execute($params){
		// 検索に使用するプラグインローダーを取得する。
		$loader = $this->getLoader();
		$loader->LoadSetting();

		// ページャの初期化
		$pager = new Clay_Pager($params->get("_pager_mode", Clay_Pager::PAGE_SLIDE), $params->get("_pager_dispmode", Clay_Pager::DISPLAY_ATTR), $params->get("_pager_per_page", 20), $params->get("_pager_displays", 3));
		$pager->importTemplates($params);
		
		// 検索条件を構築する。
		$conditions = array();
		
		// 拡張の検索条件を設定する。
		$condition = $this->getExtendedCondition($condition, $params);
		
		// タグのパラメータから検索条件を抽出
		$keys = $this->getSearchKeys();
		foreach($keys as $key){
			if($this->getDefaultByKey($key) != null || $params->check($key)){
				$condition[$this->getColumnByKey($key)] = $params->get($key, $this->getDefaultByKey($key));
			}
		}
		
		// HTTPのリクエストデータから検索条件を抽出
		foreach($keys as $key){
			if(isset($_POST["search"][$key])){
				$condition[$this->getColumnByKey($key)] = $_POST["search"][$key];
			}
		}
		
		// 並べ替え順序が指定されている場合に適用
		$sortOrder = "";
		$sortReverse = false;
		if($params->check("_sort_key")){
			$sortOrder = $params->get("_sort_key");
		}
		if(isset($_POST["_sort_key"])){
			$sortOrder = $_POST["_sort_key"];
		}
		if(empty($sortOrder)){
			$sortOrder = "create_time";
			$sortReverse = true;
		}elseif(preg_match("/^rev@/", $sortOrder) > 0){
			list($dummy, $sortOrder) = explode("@", $sortOrder);
			$sortReverse = true;
		}
		
		// 商品データを検索する。
		$model = $loader->LoadModel($this->getModelName());
		$pager->setDataSize($model->countBy($conditions));
		$model->limit($pager->getPageSize(), $pager->getCurrentFirstOffset());
		$result = $model->findAllBy($conditions, $sortOrder, $sortReverse);
		
		$_SERVER["ATTRIBUTES"][$params->get("_result", $this->getDefaultResultKey())."_pager"] = $pager;
		$_SERVER["ATTRIBUTES"][$params->get("_result", $this->getDefaultResultKey())] = $result;
	}
}
 