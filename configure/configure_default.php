<?php
// デバッグフラグ
$_SERVER["CONFIGURE"]->DEBUG = true;

// タイムゾーン
$_SERVER["CONFIGURE"]->TIMEZONE = "Asia/Tokyo";

// デフォルトロケール
$_SERVER["CONFIGURE"]->LOCALE = "ja_JP.UTF-8";

// バイナリ実行ファイルに使用するOS
$_SERVER["CONFIGURE"]->OS = "mac";

// セッション保存先
$_SERVER["CONFIGURE"]->SESSION_MANAGER = "DatabaseSessionHandler";

/* デフォルトのDB接続先 */
$_SERVER["CONFIGURE"]->connection = array(
	"default" => array("dbtype" => "mysql", "host" => "127.0.0.1", "user" => "clay", "password" => "clay", "database" => "clay", "query" => "SET NAMES utf8")
);

/* 使用するテンプレートエンジン */
$_SERVER["CONFIGURE"]->TEMPLATE_ENGINE = "SmartyTemplate";

/* 使用するロガー */
// $_SERVER["CONFIGURE"]->LOGGER = "DatabaseLogger";
?>
