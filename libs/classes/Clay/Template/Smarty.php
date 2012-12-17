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
 * このクラスの基底クラスとして使用しているSmartyが必要です。
 */
require(CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR."Smarty".DIRECTORY_SEPARATOR."Smarty.class.php");

/**
 * ページ表示用のテンプレートクラスです。
 * Smartyを継承して基本的な設定を行っています。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Clay_Template_Smarty extends Clay_Template{
    /**
	 * コンストラクタです。ページテンプレートを初期化します。
	 *
     * @access public
     */
	public function __construct(){
		// テンプレートのディレクトリとコンパイルのディレクトリをフレームワークのパス上に展開
		$this->core = new Smarty();
		$this->template_dir = $this->core->template_dir = array($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"]."/");
		$this->core->compile_dir = CLAY_ROOT.DIRECTORY_SEPARATOR."cache_smarty".DIRECTORY_SEPARATOR.$_SERVER["CONFIGURE"]->get("site_code").$_SERVER["USER_TEMPLATE"]."/";
		
		// プラグインのディレクトリを追加する。
		$smartyPath = CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR."Smarty";
		$this->core->plugins_dir[] = $smartyPath.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR;
		$this->core->plugins_dir[] = $smartyPath.DIRECTORY_SEPARATOR."sysplugins".DIRECTORY_SEPARATOR;
		$this->core->plugins_dir[] = $smartyPath.DIRECTORY_SEPARATOR."user_plugins".DIRECTORY_SEPARATOR;

		// デリミタを変更する。
		$this->core->left_delimiter = "<!--{";
		$this->core->right_delimiter = "}-->";

		// モジュール呼び出し用のフィルタを設定する。
		if(!isset($this->core->autoload_filters["pre"])){
			$this->core->autoload_filters["pre"] = array();
		}
		$this->core->autoload_filters["pre"][] = "loadmodule";
	}
	
	public function assign($tpl_var, $value = null, $nocache = false, $scope = SMARTY_LOCAL_SCOPE){
		$this->core->assign($tpl_var, $value, $nocache, $scope);
	}
	
	public function clearAssign($tpl_var){
		$this->core->clearAssign($tpl_var);
	}
	
	public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false){
		$this->core->fetch($template, $cache_id, $compile_id, $parent, $display);
	}
	
	public function loadPlugin($plugin_name, $check = true){
		$this->core->loadPlugin($plugin_name, $check);
	}
	
	public function setExceptionHandler($handler){
		$this->core->setExceptionHandler($handler);
	}
}
