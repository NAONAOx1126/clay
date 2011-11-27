<?php
/**
 * 郵便番号から住所を取得するためのJSON実装です。
 *
 * @category  JSON
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */
$loader = new PluginLoader();
$zip = $loader->loadTable("ZipsTable");
$select = new DatabaseSelect($zip);
$select->addColumn($zip->_W)->addWhere("zipcode = ?", array($_POST["zip"]));
$result = $select->execute();
?>
