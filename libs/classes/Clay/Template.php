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
 * このクラスの基底クラスとして使用しているSmartyが必要です。
 */
require(CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR."Smarty".DIRECTORY_SEPARATOR."Smarty.class.php");

/**
 * ページ表示用のテンプレートクラスです。
 * Smartyを継承して基本的な設定を行っています。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 * @since PHP 5.2
 * @version 1.0.0
 */
class Clay_Template extends Smarty{
    /**
	 * コンストラクタです。ページテンプレートを初期化します。
	 *
     * @access public
     */
	public function __construct(){
		parent::__construct();

		// テンプレートのディレクトリとコンパイルのディレクトリをフレームワークのパス上に展開
		$this->template_dir = array(FRAMEWORK_TEMPLATE_HOME."/");
		$this->compile_dir = CLAY_ROOT.DIRECTORY_SEPARATOR."cache_smarty".DIRECTORY_SEPARATOR.$_SERVER["CONFIGURE"]->get("site_code").$_SERVER["USER_TEMPLATE"]."/";
		
		// プラグインのディレクトリを追加する。
		$smartyPath = CLAY_CLASSES_ROOT.DIRECTORY_SEPARATOR."Smarty";
		$this->plugins_dir[] = $smartyPath.DIRECTORY_SEPARATOR."plugins".DIRECTORY_SEPARATOR;
		$this->plugins_dir[] = $smartyPath.DIRECTORY_SEPARATOR."sysplugins".DIRECTORY_SEPARATOR;
		$this->plugins_dir[] = $smartyPath.DIRECTORY_SEPARATOR."user_plugins".DIRECTORY_SEPARATOR;

		// デリミタを変更する。
		$this->left_delimiter = "<!--{";
		$this->right_delimiter = "}-->";

		// モジュール呼び出し用のフィルタを設定する。
		if(!isset($this->autoload_filters["pre"])){
			$this->autoload_filters["pre"] = array();
		}
		$this->autoload_filters["pre"][] = "loadmodule";
	}
	
    /**
	 * ページ出力用のメソッドをオーバーライドしています。
	 * 携帯のページについて、SJISに変換し、カナを半角にしています。
	 *
     * @access public
     */
    public function display($template, $cache_id = null, $compile_id = null, $parent = null){
		// キャッシュ無効にするヘッダを送信
		header("P3P: CP='UNI CUR OUR'");
		header("Expires: Thu, 01 Dec 1994 16:00:00 GMT");
		header("Last-Modified: ". gmdate("D, d M Y H:i:s"). " GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	
		// display template
		Clay_Logger::writeDebug("Template Dir : ".var_export($this->template_dir, true));
		Clay_Logger::writeDebug("Template Name : ".$template);
		if(Net_UserAgent_Mobile::isMobile()){
			// モバイルユーザーエージェントのインスタンスを取得
			$agent = Net_UserAgent_Mobile::singleton();
			
			// モバイルの画面サイズを取得する。
			$display = $agent->makeDisplay();
			if($display->getWidth() >= 480){
				parent::assign("isVGA", "1");
			}else{
				parent::assign("isVGA", "0");
			}
			
			// モバイルの時は出力するHTMLをデータとして取得
			$content = parent::fetch ($template, $cache_id, $compile_id, $parent, false);
			// カタカナを半角にする。
			$content = mb_convert_kana($content, "k");
			
			// ソフトバンク3GC以外の場合は、SJISエンコーディングに変換
			if(!$agent->isSoftbank() || !$agent->isType3GC()){
				header("Content-Type: text/html; charset=Shift_JIS");
				if(preg_match("/<meta\\s+http-equiv\\s*=\\s*\"Content-Type\"\\s+content\\s*=\\s*\"([^;]+);\\s*charset=utf-8\"\\s*\\/?>/i", $content, $params) > 0){
					header("Content-Type: ".$params[1]."; charset=Shift_JIS");
					$content = str_replace($params[0], "<meta http-equiv=\"Content-Type\" content=\"".$params[1]."; charset=Shift_JIS\" />", $content);
				}else{
					header("Content-Type: text/html; charset=Shift_JIS");
				}
				echo mb_convert_encoding($content, "Shift_JIS", "UTF-8");
			}else{
				header("Content-Type: text/html; charset=UTF-8");
				echo $content;
			}
		}else{
			header("Content-Type: text/html; charset=UTF-8");
			parent::fetch ($template, $cache_id, $compile_id, $parent, true);
		}
    } 
}
 