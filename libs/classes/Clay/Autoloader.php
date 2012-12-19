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
 * フレームワークのクラスの自動ローディングを制御するクラス
 * 
 * @package Base
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Autoloader{
	/**
	 * クラスの自動ローディング処理を登録する。
	 */
	public static function register() {
		return spl_autoload_register(array('Clay_Autoloader', 'load'));
	}

	/**
	 * クラスの自動ローディング処理の実装
	 */
	public static function load($class){
		// クラスが読み込み済みかClay_で始まっていない場合は読み込みの対象外とする。
		if ((class_exists($class)) || (strpos($class, 'Clay') !== 0)) {
			return false;
		}

		// クラスの読み込み先を取得する。
		$classPath = CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR.str_replace("_", DIRECTORY_SEPARATOR, $class).".php";

		// クラスのファイルが存在していないか読み込み不可能の場合は読み込みの対象外とする。
		if ((file_exists($classPath) === false) || (is_readable($classPath) === false)) {
			return false;
		}

		// クラスを読み込む
		require($classPath);
	}
}