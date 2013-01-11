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
 * 各種モジュールを読み込むためのクラスです。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Plugin{
	/**
	 * 読み込む先のネームスペース
	 */
	private $namespace;
	
	/**
	 * コンストラクタです。
	 */
	public function __construct($namespace = DEFAULT_PACKAGE_NAME){
		$this->namespace = $namespace;
	}
		
	/**
	 * 拡張ライブラリファイルを読み込む
	 * @param string $type 拡張ファイルの種別
	 * @param string $name 拡張ファイルのオブジェクト名
	 */
	private function load($type, $name, $params = array()){
		try{
			if(!empty($this->namespace)){
				$name = $this->namespace.".".$name;
			}
			Clay_Logger::writeDebug($name." ==> ".number_format(memory_get_usage()));
			$names = explode(".", $name);
			$class = implode("_", $names);
			$path = implode(DIRECTORY_SEPARATOR, $names);
			if(class_exists($class)){
				return new $class($params);
			}
			if(isset($_SERVER["CONFIGURE"]->site_home)){
				$pluginPath = $_SERVER["CONFIGURE"]->site_home;
				if(!empty($pluginPath)){
					if(file_exists($pluginPath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$path.".php")){
						Clay_Logger::writeDebug("Loaded File for ".$class." class : ".$pluginPath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$path.".php");
						require_once($pluginPath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$path.".php");
						$cls = new $class($params);
						Clay_Logger::writeDebug("Loading File for ".$class." class : ".$pluginPath.DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$path.".php");
						return $cls;
					}
				}
			}
			array_splice($names, 1, 0, array($type));
			$names[0] = strtolower($names[0]);
			$path = "clay_".implode(DIRECTORY_SEPARATOR, $names);
			if(file_exists(CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR.$path.".php")){
				Clay_Logger::writeDebug("Loading File for ".$class." class : ".CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR.$path.".php");
				require_once(CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR.$path.".php");
				$cls = new $class($params);
				Clay_Logger::writeDebug("Loaded File for ".$class." class : ".CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR.$path.".php");
				return $cls;
			}
			Clay_Logger::writeDebug("No Plugin File for ".$class." class : ".CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR.$path.".php");
			return null;
		}catch(Exception $e){
			Clay_Logger::writeError("Failed to load plugin", $e);
		}
	}
	
	/**
	 * モジュールクラスのファイルを読み込む
	 *
	 * @params string $name モジュール呼び出し名
	 */
	function loadSetting(){
		$names = explode(".", $this->namespace);
		if(isset($_SERVER["CONFIGURE"]->site_home)){
			$pluginPath = $_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR.$names[0];
			if(!empty($pluginPath)){
				if(file_exists($pluginPath."/Setting.php")){
					Clay_Logger::writeDebug("Loaded File for Setting : ".$pluginPath."/Setting.php");
					require_once($pluginPath."/Setting.php");
					return;
				}
			}
		}
		$names[0] = strtolower($names[0]);
		if(file_exists(CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR."clay_".$names[0]."/Setting.php")){
			Clay_Logger::writeDebug("Loaded File for Setting : ".CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR."clay_".$names[0]."/Setting.php");
			require_once(CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR."clay_".$names[0]."/Setting.php");
			return;
		}
	}
	
	/**
	 * モジュールクラスのファイルを読み込む
	 *
	 * @params string $name モジュール呼び出し名
	 */
	function loadModule($name, $params = array()){
		return $this->load("modules", $name, $params);
	}
	
	/**
	 * モデルクラスのファイルを読み込む
	 *
	 * @params string $name モデル呼び出し名
	 */
	function loadModel($name, $params = array()){
		return $this->load("models", $name, $params);
	}
	
	/**
	 * テーブルクラスのファイルを読み込む
	 *
	 * @params string $name テーブル呼び出し名
	 */
	function loadTable($name){
		return $this->load("tables", $name);
	}
	
	/**
	 * バッチクラスのファイルを読み込む
	 *
	 * @params string $name バッチ呼び出し名
	 */
	function loadBatch($name){
		return $this->load("batch", $name);
	}
	
	/**
	 * JSONクラスのファイルを読み込む
	 *
	 * @params string $name JSON呼び出し名
	 */
	function loadJson($name){
		return $this->load("json", $name);
	}
	
	/**
	 * テストクラスのファイルを読み込む
	 *
	 * @params string $name テスト呼び出し名
	 */
	function loadTest($name){
		return $this->load("modules_test", $name);
	}
}
 