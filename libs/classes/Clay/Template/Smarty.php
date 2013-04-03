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
	private $template;
	
    /**
	 * コンストラクタです。ページテンプレートを初期化します。
	 *
     * @access public
     */
	public function __construct(){
		// コアの処理を設定する。
		$this->core = new Smarty();
		
		// テンプレートのディレクトリとコンパイルのディレクトリをフレームワークのパス上に展開
		$this->template_dir = $this->core->template_dir = array($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"]."/");
		$this->core->compile_dir = CLAY_ROOT.DIRECTORY_SEPARATOR."_cache_smarty".DIRECTORY_SEPARATOR.$_SERVER["CONFIGURE"]->site_code.$_SERVER["USER_TEMPLATE"]."/";
		
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
		
		// デフォルトのアサインを設定
		$this->initialAssign();
	}
	
	public function assign($tpl_var, $value = null, $nocache = false, $scope = SMARTY_LOCAL_SCOPE){
		return $this->core->assign($tpl_var, $value, $nocache, $scope);
	}
	
	public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false){
		return $this->core->fetch($template, $cache_id, $compile_id, $parent, $display);
	}
}
