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

/**
 * VGA画像をQVGA画像に変換するためのメインPHPです。
 */

// 共通のライブラリの呼び出し。
require(dirname(__FILE__)."/libs/require.php");

// ドキュメントルートの最後にスラッシュを追加
if(substr($_SERVER["DOCUMENT_ROOT"], -1) != "/"){
	$_SERVER["DOCUMENT_ROOT"] .= "/";
}

// VGAのファイルパスの設定を取得
$image480 = $_SERVER["DOCUMENT_ROOT"]."contents/".$_SERVER["SERVER_NAME"]."/mobile/".$_GET["image"];

// QVGAのファイルパスの設定を取得
$info = getimagesize($image480);
$image240 = $_SERVER["DOCUMENT_ROOT"]."contents/".$_SERVER["SERVER_NAME"]."/qvga/".$_GET["image"];

// display template
$image = $image480;
if(Net_UserAgent_Mobile::isMobile()){
	// モバイルユーザーエージェントのインスタンスを取得
	$agent = Net_UserAgent_Mobile::singleton();
	
	// モバイルの画面サイズを取得する。
	$display = $agent->makeDisplay();
	if($display->getWidth() < 480){
		// QVGA画像用のフォルダを作成する。
		$path = pathinfo($image240);
		$pathParts = explode("/", $path["dirname"]);
		$path = "";
		$docroot = false;
		foreach($pathParts as $part){
			$path .= $part."/";
			if(!$docroot && $path != $_SERVER["DOCUMENT_ROOT"]){
				continue;
			}
			$docroot = true;
			if(!is_dir($path)){
				if(!mkdir($path, 0777)){
					header("HTTP/1.1 404 Not Found");
					echo $path.": can not make directory.";
					exit;
				}
			}
		}
		
		// 画像を縮小する。
		resize_image($image480, $image240, ceil($info[0] / 2), ceil($info[1] / 2));
		
		// アクセスする端末に応じた画像を出力する。
		$image = $image240;
	}
}
if(file_exists($image)){
	header("Content-Type: ".$info["mime"]);
	echo file_get_contents($image);
}else{
	header("HTTP/1.1 404 Not Found");
}
