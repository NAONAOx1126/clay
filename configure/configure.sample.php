<?php
// デバッグフラグ
$_SERVER["CONFIGURE"]->DEBUG = true;

// エラー表示オプション
$_SERVER["CONFIGURE"]->DISPLAY_ERROR = "On";

// タイムゾーン
$_SERVER["CONFIGURE"]->TIMEZONE = "Asia/Tokyo";

// デフォルトロケール
$_SERVER["CONFIGURE"]->LOCALE = "ja_JP.UTF-8";

// バイナリ実行ファイルに使用するOS
$_SERVER["CONFIGURE"]->OS = "mac";

// セッション保存先
//$_SERVER["CONFIGURE"]->SESSION_MANAGER = "DatabaseSessionHandler";
$_SERVER["CONFIGURE"]->SESSION_MANAGER = "";

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

/* モジュールのDB接続 */
$_SERVER["CONFIGURE"]->connection_modules = array(
);

/* 使用するテンプレートエンジン */
$_SERVER["CONFIGURE"]->TEMPLATE_ENGINE = "Clay_Template_Smarty";

/* 使用するテンプレートのパス */
$_SERVER["CONFIGURE"]->site_home = "/Library/WebServer/Documents/clay_mroc";

/* キャッシュの接続先 */
$_SERVER["CONFIGURE"]->MEMCACHED_SERVER = "localhost";

/* JSON API実行時に使用するキー */
$_SERVER["CONFIGURE"]->JSON_API_KEY = "";

/* 使用するプレフィルタ */
$_SERVER["CONFIGURE"]->prefilters = array(
);

/* 使用するポストフィルタ */
$_SERVER["CONFIGURE"]->postfilters = array(
);

/* フェイスブックのアカウント情報 */
$_SERVER["CONFIGURE"]->facebook = array(
	"protocol" => "http://",
	"appId" => "430115277069192",
	"secret" => "eb60016d3a1753c3bde782cb5f6e9934",
	"cookie" => true,
	"permissions" => array(
		"email", "publish_actions", "user_about_me", "user_actions.music", 
		"user_actions.news", "user_actions.video", "user_activities", "user_birthday", 
		"user_education_history", "user_events", "user_games_activity", "user_groups", 
		"user_hometown", "user_interests", "user_likes", "user_location", "user_notes", 
		"publish_actions", "user_relationship_details", "user_relationships", 
		"email", "publish_actions", "email", "publish_actions",
		"user_photos", "user_questions", "email", "publish_actions", "email", "publish_actions",
		"user_religion_politics", "user_status", "user_subscriptions", "user_videos", 
		"user_website", "user_work_history",
		"friends_about_me", "friends_actions.music", "friends_actions.news", 
		"friends_actions.video", "friends_activities", "friends_birthday",
		"friends_education_history", "friends_events", "friends_games_activity", 
		"publish_actions", "email", "publish_actions",
		"friends_groups", "friends_hometown", "friends_interests", "friends_likes", 
		"friends_location", "friends_notes", "friends_photos", "friends_questions", 
		"friends_relationship_details", "friends_relationships", "friends_religion_politics", 
		"friends_status", "friends_subscriptions", "friends_videos", "friends_website", 
		"friends_work_history", 
		// "manage_groups",
		"ads_management", "create_event", "create_note", "export_stream", 
		"friends_online_presence", "manage_friendlists", "manage_notifications", 
		"manage_notifications", "manage_pages", "photo_upload", "publish_checkins", 
		"publish_stream", "read_friendlists", "read_insights", "read_mailbox", 
		"read_page_mailboxes", "read_requests", "read_stream", "rsvp_event",
		"share_item", "sms", "status_update", "user_online_presence", "video_upload", 
		"xmpp_login"
	)
);
