<?php
/**
 * システム関連の設定
 */
// タイムゾーン
$_SERVER["CONFIGURE"]->TIMEZONE = "Asia/Tokyo";

// デフォルトロケール
$_SERVER["CONFIGURE"]->LOCALE = "ja_JP.UTF-8";

// プルダウンに使える月リスト
$_SERVER["CONFIGURE"]->select_months = array();
for($i = 1; $i <= 12; $i ++){
	$_SERVER["CONFIGURE"]->select_months[sprintf("%02d", $i)] = $i."月";
}

// プルダウンに使える日リスト
$_SERVER["CONFIGURE"]->select_days = array();
for($i = 1; $i <= 31; $i ++){
	$_SERVER["CONFIGURE"]->select_days[sprintf("%02d", $i)] = $i."日";
}


/**
 * デバッグエラー関連の設定
 */
// デバッグフラグ
$_SERVER["CONFIGURE"]->DEBUG = true;

// エラー表示オプション
$_SERVER["CONFIGURE"]->DISPLAY_ERROR = "On";

/**
 * セッション関連の設定
 */
// セッション保存先
$_SERVER["CONFIGURE"]->SESSION_MANAGER = "";

/**
 * テンプレート関連の設定
 */
/* 使用するテンプレートエンジン */
$_SERVER["CONFIGURE"]->TEMPLATE_ENGINE = "Clay_Template_Smarty";

/* プラグインのパス */
$_SERVER["CONFIGURE"]->plugins_root = realpath(dirname(__FILE__)."/../../")."/clay_plugins";

/* 使用するテンプレートのパス */
$_SERVER["CONFIGURE"]->site_home = realpath(dirname(__FILE__)."/../../")."/clay_demo";

/**
 * データベース関連の設定
 */
/* デフォルトのDB接続先 */
$_SERVER["CONFIGURE"]->connections = array(
);

/* モジュールのDB接続 */
$_SERVER["CONFIGURE"]->connection_modules = array(
);

/**
 * memcache関連の設定
 */
/* キャッシュの接続先 */
$_SERVER["CONFIGURE"]->MEMCACHED_SERVER = "";

/**
 * JSONP関連の設定
 */
/* JSON API実行時に使用するキー */
$_SERVER["CONFIGURE"]->JSON_API_KEY = "";

/**
 * フィルタ関連の設定
 */
/* 使用するプレフィルタ */
$_SERVER["CONFIGURE"]->prefilters = array(
);

/* 使用するポストフィルタ */
$_SERVER["CONFIGURE"]->postfilters = array(
);

/**
 * Facebook関連の設定
 */
/* フェイスブックのアカウント情報 */
$_SERVER["CONFIGURE"]->facebook = array(
		"protocol" => "http://",
		"appId" => "1234567890",
		"secret" => "xxxxxxxxxxxxxxxxxxxxxxxxxx",
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

/**
 * サイトのデフォルト設定
 */
/* サイト名 */
$_SERVER["CONFIGURE"]->site_code = "";

/* サイト名 */
$_SERVER["CONFIGURE"]->site_name = "";

/* ドメイン名 */
$_SERVER["CONFIGURE"]->domain_name = "";
