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
 * DoCoMoの携帯端末情報を取得するためのクラスです。
 *
 * @package Mobile
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Mobile_Docomo extends Clay_Mobile{
	/**
	 * モバイルの端末情報取得クラスを作成する。
	 */
	public static function create($info){
		if($info == null){
			if(preg_match("/^DoCoMo/i", $_SERVER["HTTP_USER_AGENT"]) > 0){
				return new Clay_Mobile_Docomo();
			}
		}
		return $info;
	}
	
	public function __construct(){
		$this->isMobile = true;
		$this->isFuturePhone = true;
		$this->isSmartPhone = false;
		$this->deviceType = "DoCoMo";
		if(isset($_SERVER["HTTP_X_DCMGUID"])){
			$this->mobileId = $_SERVER["HTTP_X_DCMGUID"];
		}else{
			if($_SERVER["REQUEST_METHOD"] == "GET" && preg_match("/guid=on/", $_SERVER["QUERY_STRING"]) == 0){
				if(strpos($_SERVER["REQUEST_URI"], "?") !== FALSE){
					header("Location: ".$_SERVER["REQUEST_URI"]."&guid=on");
				}else{
					header("Location: ".$_SERVER["REQUEST_URI"]."?guid=on");
				}
				exit;
			}
		}
		$this->screenWidth = 0;
		$this->screenHeight = 0;
	}
}
 