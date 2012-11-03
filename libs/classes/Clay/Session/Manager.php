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
 * セッションハンドラを管理するためのクラスです。
 *
 * @package Session
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Session_Manager{
    public static function create($handler){
        session_set_save_handler(
            array($handler, "open"),
            array($handler, "close"),
            array($handler, "read"),
            array($handler, "write"),
            array($handler, "destroy"),
            array($handler, "clean")
        );
    }
}
 