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
 * ページ表示用のテンプレートクラスです。
 * Smartyを継承して基本的な設定を行っています。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Clay_Template_Phptal extends Clay_Template{
    /**
	 * コンストラクタです。ページテンプレートを初期化します。
	 *
     * @access public
     */
	public function __construct(){
		$this->core = new PHPTAL();

		// テンプレートのディレクトリとコンパイルのディレクトリをフレームワークのパス上に展開
		$this->template_dir = array($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"]."/");
	}
	
	
	public function assign($tpl_var, $value = null, $nocache = false, $scope = null){
		$this->core->$tpl_var = $value;
	}
	
	public function fetch($template, $cache_id = null, $compile_id = null, $parent = null, $display = false){
		$templateFile = "";
		foreach($this->template_dir as $dir){
			if(file_exists($dir.DIRECTORY_SEPARATOR.$template)){
				$templateFile = $dir.DIRECTORY_SEPARATOR.$template;
				break;
			}
		}
		// テンプレートが取得できた場合は表示テンプレートに設定
		if(!empty($templateFile)){
			$this->core->setTemplate($templateFile);
		}else{
			throw new SystemException("Template Not Found IN (".implode(", ", $this->template_dir).")");
		}
		
		// プレフィルタを設定
		$filter = new Clay_Template_Phptal_PreFilter();
		$filter->add(new Clay_Template_Phptal_Loadmodule());
		$this->core->setPreFilter($filter);
		
		// 結果を返却
		if($display){
			echo $this->core->execute();
		}else{
			return $this->core->execute();
		}
	}
}
 