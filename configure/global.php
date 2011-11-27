<?php
// デバッグフラグ
$_SERVER["CONFIGURE"]->DEBUG = true;

// デバッグフラグ
$_SERVER["CONFIGURE"]->TIMEZONE = "Asia/Tokyo";

// デフォルトロケール
$_SERVER["CONFIGURE"]->LOCALE = "ja_JP.UTF-8";

// 管理画面のURL
$_SERVER["CONFIGURE"]->ADMIN_TOOLS = "_admin";

// バイナリ実行ファイルに使用するOS
$_SERVER["CONFIGURE"]->OS = "mac";

// セッション保存先
$_SERVER["CONFIGURE"]->SESSION_MANAGER = "DatabaseSessionHandler";

/* デフォルトのDB接続先 */
$_SERVER["CONFIGURE"]->connection = array("default" => array("dbtype" => "mysql", "host" => "127.0.0.1", "user" => "clay", "password" => "clay", "database" => "clay", "query" => "SET NAMES utf8"));
?>
