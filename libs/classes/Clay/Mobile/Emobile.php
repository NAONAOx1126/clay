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
 * E-Mobileの携帯端末情報を取得するためのクラスです。
 *
 * @package Mobile
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Mobile_Emobile extends Clay_Mobile{
	/**
	 * モバイルの端末情報取得クラスを作成する。
	 */
	public static function create($info){
		if($info == null){
			if(preg_match("/^(emobile|Huawei|IAC)/i", $_SERVER["HTTP_USER_AGENT"]) > 0){
				return new Clay_Mobile_Emobile();
			}
		}
		return $info;
	}
	
	public function __construct(){
		$this->isMobile = true;
		$this->isFuturePhone = true;
		$this->isSmartPhone = false;
		$this->deviceType = "E-Mobile";
		$this->mobileId = $_SERVER["HTTP-X-EM-UID"];
		$this->screenWidth = 0;
		$this->screenHeight = 0;
	}
}
 