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
 * セッションハンドラのインターフェイスです。
 *
 * @package Session
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
abstract class Clay_Session_Handler{
	abstract public function open($savePath, $sesionName);
	
	abstract public function close();
	
	abstract public function read($id);
	
	abstract public function write($id, $data);
	
	abstract public function destroy($id);
	
	abstract public function clean($maxlifetime);
}
 