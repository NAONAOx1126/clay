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
 * このシステムにおける全てのモジュールの基底クラスになります。
 * 必ず拡張する必要があり、executeメソッドを実装する必要があります。
 *
 * @package Plugin
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Plugin_Module{
    /**
	 * デフォルト実行のメソッドになります。
	 * このメソッド以外がモジュールとして呼ばれることはありません。
     *
     * @param array $params モジュールの受け取るパラメータ
     * @access public
     */
	abstract function execute($params);
	
	protected function encryptPassword($login_id, $plain_password){
		return sha1($login_id.":".$plain_password);
	}
	
	protected function removeInput($key){
		unset($_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY][$key]);
		unset($_POST[$key]);
	}
	
	protected function redirect($url){
		if(is_array($_POST)){
			$_SESSION["INPUT_DATA"] = array(TEMPLATE_DIRECTORY => array());
			foreach($_POST as $key => $value){
				$_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY][$key] = $value;
			}
		}
		header("Location: ".$url);
		exit;
	}
	
	protected function reload(){
		$this->redirect(CLAY_SUBDIR.$_SERVER["TEMPLATE_NAME"]);
	}
}
 