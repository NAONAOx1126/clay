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
 
/** Zendのローダーの読み込み */
if (!defined('Zend_ROOT')) {
	define('Zend_ROOT', realpath(dirname(__FILE__)));
	require(Zend_ROOT.DIRECTORY_SEPARATOR.'Zend'.DIRECTORY_SEPARATOR.'Loader.php');
	ZendLoader::registerAutoload();
}
 