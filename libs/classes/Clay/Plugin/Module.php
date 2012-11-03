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
	
	function reload(){
		header("Location: ".FRAMEWORK_URL_BASE.$_SERVER["TEMPLATE_NAME"]);
		exit;
	}
}
 