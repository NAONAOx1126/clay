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
 * URLからテンプレートパスを取得するための起動処理です。
 * 
 * @package Bootstrap
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Bootstrap_TemplateName{
	public static function start(){
		// REQUEST URIから実際に出力するテンプレートファイルを特定
		$_SERVER["TEMPLATE_NAME"] = str_replace("?".$_SERVER["QUERY_STRING"], "", $_SERVER["REQUEST_URI"]);
		if(CLAY_SUBDIR != ""){
			if(strpos($_SERVER["TEMPLATE_NAME"], CLAY_SUBDIR) === 0){
				$_SERVER["TEMPLATE_NAME"] = substr($_SERVER["TEMPLATE_NAME"], strlen(CLAY_SUBDIR));
			}	
		}
		
		// テンプレートにシンボリックリンクを作成する。
		if(isset($_SERVER["CONFIGURE"]->site_home) && $_SERVER["CONFIGURE"]->site_home != ""){
			if(!file_exists(CLAY_ROOT.DIRECTORY_SEPARATOR."contents".DIRECTORY_SEPARATOR.$_SERVER["SERVER_NAME"])){
				Clay_Logger::writeDebug("CREATE SYMBOLIC LINK : ".CLAY_ROOT.DIRECTORY_SEPARATOR."contents".DIRECTORY_SEPARATOR.$_SERVER["SERVER_NAME"]." => ".$_SERVER["CONFIGURE"]->get("site_home"));
				symlink($_SERVER["CONFIGURE"]->site_home, CLAY_ROOT.DIRECTORY_SEPARATOR."contents".DIRECTORY_SEPARATOR.$_SERVER["SERVER_NAME"]);
			}
			if(is_writable($_SERVER["CONFIGURE"]->site_home)){
				if(!file_exists($_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR."mobile")){
					symlink($_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR."templates", $_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR."mobile");
				}
				if(!file_exists($_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR."sphone")){
					symlink($_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR."templates", $_SERVER["CONFIGURE"]->site_home.DIRECTORY_SEPARATOR."sphone");
				}
			}
		}
		
		// ユーザーのテンプレートを取得する。
		if(isset($_SERVER["CLIENT_DEVICE"])){
			if($_SERVER["CLIENT_DEVICE"]->getCapability("is_wireless_device") == "true"){
				if($_SERVER["CLIENT_DEVICE"]->getCapability("brand_name") == "Apple" && $_SERVER["CLIENT_DEVICE"]->getCapability("brand_name") == "Android"){
					$_SERVER["CLIENT_DEVICE_TYPE"] = "SmartPhone";
					$_SERVER["USER_TEMPLATE"] = "/sphone";
				}else{
					$_SERVER["CLIENT_DEVICE_TYPE"] = "FuturePhone";
					$_SERVER["USER_TEMPLATE"] = "/mobile";
				}
			}else{
				$_SERVER["CLIENT_DEVICE_TYPE"] = "PC";
				$_SERVER["USER_TEMPLATE"] = "/templates";
			}
		}else{
			$_SERVER["CLIENT_DEVICE_TYPE"] = "PC";
			$_SERVER["USER_TEMPLATE"] = "/templates";
		}
		
		// テンプレートがディレクトリかどうか調べ、ディレクトリの場合はファイル名に落とす。
		// 呼び出し先がディレクトリで最後がスラッシュでない場合は最後にスラッシュを補完
		if(is_dir($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"])){
			if(is_dir($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]) && substr($_SERVER["TEMPLATE_NAME"], -1) != "/" ){
				$_SERVER["TEMPLATE_NAME"] .= "/";
			}
			if(substr($_SERVER["TEMPLATE_NAME"], -1) == "/"){
				if(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]."index.html")){
					$_SERVER["TEMPLATE_NAME"] .= "index.html";
				}elseif(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]."index.htm")){
					$_SERVER["TEMPLATE_NAME"] .= "index.htm";
				}elseif(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]."index.xml")){
					$_SERVER["TEMPLATE_NAME"] .= "index.xml";
				}
			}
		}
		
		if(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]) || is_dir($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"])){
			if(is_dir($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]) && substr($_SERVER["TEMPLATE_NAME"], -1) != "/" ){
				$_SERVER["TEMPLATE_NAME"] .= "/";
			}
			// 呼び出し先がスラッシュで終わっている場合にはファイル名を補完
			if(substr($_SERVER["TEMPLATE_NAME"], -1) == "/"){
				if(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]."index.html")){
					$_SERVER["TEMPLATE_NAME"] .= "index.html";
				}elseif(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]."index.htm")){
					$_SERVER["TEMPLATE_NAME"] .= "index.htm";
				}elseif(file_exists($_SERVER["CONFIGURE"]->site_home.$_SERVER["USER_TEMPLATE"].$_SERVER["TEMPLATE_NAME"]."index.xml")){
					$_SERVER["TEMPLATE_NAME"] .= "index.xml";
				}
			}
		}
	}
}
 