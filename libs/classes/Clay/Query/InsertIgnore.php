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
 * データベース排他挿入処理用のクラスです。
 *
 * @package Query
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Query_InsertIgnore extends Clay_Query_InsertBase{
	protected function getPrefix(){
		return "INSERT IGNORE";
	}
}
 