<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */

// セッション管理クラスをインクルード
switch($_SERVER["CONFIGURE"]->get("SESSION_MANAGER")){
	case "":
		ini_set("session.save_handler", "files");
		break;
	case "memcached":
		ini_set("session.save_handler", "memcache");
		ini_set("session.save_path", "localhost:11211");	
		break;
	default:
		ini_set("session.save_handler", "user");
		$manager = "Clay_Session_Handler_".str_replace("SessionHandler", "", $_SERVER["CONFIGURE"]->get("SESSION_MANAGER"));
		Clay_Session_Manager::create(new $manager());
		break;
}

// 拡張モジュールのテーブルディレクトリ
$_SERVER["FRAMEWORK_SITE_HOME"] = $_SERVER["CONFIGURE"]->get("site_home");
define("FRAMEWORK_SITE_HOME", $_SERVER["FRAMEWORK_SITE_HOME"]);

// テンプレートにシンボリックリンクを作成する。
if($_SERVER["CONFIGURE"]->get("site_home") != ""){
	if(!file_exists(FRAMEWORK_CONTENTS_HOME."/".$_SERVER["SERVER_NAME"])){
		Clay_Logger::writeDebug("CREATE SYMBOLIC LINK : ".FRAMEWORK_CONTENTS_HOME."/".$_SERVER["SERVER_NAME"]." => ".$_SERVER["CONFIGURE"]->get("site_home"));
		symlink($_SERVER["CONFIGURE"]->get("site_home"), FRAMEWORK_CONTENTS_HOME."/".$_SERVER["SERVER_NAME"]);
	}
	if(is_writable($_SERVER["CONFIGURE"]->get("site_home"))){
		if(!file_exists($_SERVER["CONFIGURE"]->get("site_home")."/mobile/")){
			symlink($_SERVER["CONFIGURE"]->get("site_home")."/templates", $_SERVER["CONFIGURE"]->get("site_home")."/mobile");
		}
		if(!file_exists($_SERVER["CONFIGURE"]->get("site_home")."/iphone/")){
			symlink($_SERVER["CONFIGURE"]->get("site_home")."/templates", $_SERVER["CONFIGURE"]->get("site_home")."/iphone");
		}
		if(!file_exists($_SERVER["CONFIGURE"]->get("site_home")."/android/")){
			symlink($_SERVER["CONFIGURE"]->get("site_home")."/templates", $_SERVER["CONFIGURE"]->get("site_home")."/android");
		}
	}
}

// ユーザーエージェントを取得する（携帯判定）
require_once("Net/UserAgent/Mobile.php");
$_SERVER["CLIENT"]["AGENT"] = Net_UserAgent_Mobile::factory();
$_SERVER["CLIENT"]["MODEL"] = $_SERVER["CLIENT"]["AGENT"]->getModel();

// ユーザーのテンプレートを取得する。
if(isset($_SERVER["HTTP_USER_AGENT"])){
	if(Net_UserAgent_Mobile::isMobile()){
		$_SERVER["USER_TEMPLATE"] = "/mobile";
	}elseif(preg_match("/^Mozilla\\/5\\.0 \\((iPod|iPhone|iPad);/", $_SERVER["HTTP_USER_AGENT"]) > 0){
		$_SERVER["USER_TEMPLATE"] = "/iphone";
	}elseif(preg_match("/^Mozilla\\/5\\.0 \\(Linux; U; Android/", $_SERVER["HTTP_USER_AGENT"]) > 0){
		$_SERVER["USER_TEMPLATE"] = "/android";
	}else{
		$_SERVER["USER_TEMPLATE"] = "/templates";
	}
	if(!is_dir(FRAMEWORK_SITE_HOME.$_SERVER["USER_TEMPLATE"]) && !is_dir(CLAY_ROOT.$_SERVER["USER_TEMPLATE"])){
		$_SERVER["USER_TEMPLATE"] = "/templates";
	}
}else{
	$_SERVER["USER_TEMPLATE"] = "/templates";
}

// 拡張モジュールのテンプレートディレクトリ
$_SERVER["FRAMEWORK_TEMPLATE_HOME"] = FRAMEWORK_SITE_HOME.$_SERVER["USER_TEMPLATE"];
define("FRAMEWORK_TEMPLATE_HOME", $_SERVER["FRAMEWORK_TEMPLATE_HOME"]);	

// テンプレートがディレクトリかどうか調べ、ディレクトリの場合はファイル名に落とす。
// 呼び出し先がディレクトリで最後がスラッシュでない場合は最後にスラッシュを補完
if(is_dir(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"])){
	if(is_dir(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"]) && substr($_SERVER["TEMPLATE_NAME"], -1) != "/" ){
		$_SERVER["TEMPLATE_NAME"] .= "/";
	}
	if(substr($_SERVER["TEMPLATE_NAME"], -1) == "/"){
		if(file_exists(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"]."index.html")){
			$_SERVER["TEMPLATE_NAME"] .= "index.html";
		}elseif(file_exists(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"]."index.htm")){
			$_SERVER["TEMPLATE_NAME"] .= "index.htm";
		}elseif(file_exists(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"]."index.xml")){
			$_SERVER["TEMPLATE_NAME"] .= "index.xml";
		}
	}
}
if(!file_exists(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"]) || is_dir(FRAMEWORK_TEMPLATE_HOME.$_SERVER["TEMPLATE_NAME"])){
	if(is_dir(CLAY_ROOT."/templates/".$_SERVER["TEMPLATE_NAME"]) && substr($_SERVER["TEMPLATE_NAME"], -1) != "/" ){
		$_SERVER["TEMPLATE_NAME"] .= "/";
	}
	// 呼び出し先がスラッシュで終わっている場合にはファイル名を補完
	if(substr($_SERVER["TEMPLATE_NAME"], -1) == "/"){
		if(file_exists(CLAY_ROOT."/templates/".$_SERVER["TEMPLATE_NAME"]."index.html")){
			$_SERVER["TEMPLATE_NAME"] .= "index.html";
		}elseif(file_exists(CLAY_ROOT."/templates/".$_SERVER["TEMPLATE_NAME"]."index.htm")){
			$_SERVER["TEMPLATE_NAME"] .= "index.htm";
		}elseif(file_exists(CLAY_ROOT."/templates/".$_SERVER["TEMPLATE_NAME"]."index.xml")){
			$_SERVER["TEMPLATE_NAME"] .= "index.xml";
		}
	}
}

// テンプレートのディレクトリを取得する。
$_SERVER["TEMPLATE_DIRECTORY"] = substr($_SERVER["TEMPLATE_NAME"], 0, strrpos($_SERVER["TEMPLATE_NAME"], "/"));
define("TEMPLATE_DIRECTORY", $_SERVER["TEMPLATE_DIRECTORY"]);

// コンテンツのルートをURLを取得する。
$_SERVER["ROOT"] = FRAMEWORK_URL_HOME."/contents/".$_SERVER["SERVER_NAME"].$_SERVER["USER_TEMPLATE"];
define("ROOT", $_SERVER["ROOT"]);
?>
