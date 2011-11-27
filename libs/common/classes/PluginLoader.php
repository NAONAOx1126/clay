<?php
/**
 * 各種モジュールを読み込むためのクラスです。
 *
 * @category  Loader
 * @package   Common
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

/**
 * 各種モジュールを読み込むためのクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class PluginLoader{
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
		if(!empty($this->namespace)){
			$name = $this->namespace.".".$name;
		}
		Logger::writeDebug($name);
		$names = explode(".", $name);
		$path = implode("/", $names);
		$class = implode("_", $names);
		if(class_exists($class)){
			return new $class($params);
		}
		if(defined("FRAMEWORK_SITE_HOME")){
			if(file_exists(FRAMEWORK_SITE_HOME."/".$type."/".$path.".php")){
				Logger::writeDebug("Loaded File for ".$class." class : ".FRAMEWORK_SITE_HOME."/".$type."/".$path.".php");
				require_once(FRAMEWORK_SITE_HOME."/".$type."/".$path.".php");
				return new $class($params);
			}
		}
		array_splice($names, 1, 0, array($type));
		$path = implode("/", $names);
		if(file_exists(FRAMEWORK_PLUGIN_HOME."/".$path.".php")){
			Logger::writeDebug("Loaded File for ".$class." class : ".FRAMEWORK_PLUGIN_HOME."/".$path.".php");
			require_once(FRAMEWORK_PLUGIN_HOME."/".$path.".php");
			return new $class($params);
		}
		Logger::writeDebug("No Plugin File for ".$class." class.");
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
	function loadModel($name){
		return $this->load("models", $name);
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
?>