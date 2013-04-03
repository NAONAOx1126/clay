<?php
/* サイト名 */
$_SERVER["CONFIGURE"]->site_code = "demo";

/* サイト名 */
$_SERVER["CONFIGURE"]->site_name = "デモサイト";

/* ドメイン名 */
$_SERVER["CONFIGURE"]->domain_name = "localhost";

/* デフォルトのDB接続先 */
$_SERVER["CONFIGURE"]->connections = array(
	"default" => array("dbtype" => "mysql", "host" => "127.0.0.1", "user" => "clay", "password" => "clay", "database" => "clay", "query" => "SET NAMES utf8")
);

/* 使用するテンプレートのパス */
$_SERVER["CONFIGURE"]->site_home = "/Library/WebServer/Documents/clay_demo";
