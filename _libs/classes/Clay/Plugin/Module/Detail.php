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
 * 詳細表示用のモジュールです。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Plugin_Module_Detail extends Clay_Plugin_Module{
	protected function executeImpl($type, $name, $value, $result){
		// サイトデータを取得する。
		$loader = new Clay_Plugin($type);
		$model = $loader->loadModel($name);
		$model->findByPrimaryKey($value);
		
		$_SERVER["ATTRIBUTES"][$result] = $model;
	}
}
 