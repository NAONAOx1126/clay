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
 * アクセスした携帯端末情報を取得するためのクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Mobile{
	protected $isMobile;
	
	protected $isFuturePhone;
	
	protected $isSmartPhone;
	
	protected $deviceType;
	
	protected $mobileId;
	
	protected $screenWidth;
	
	protected $screenHeight;
	
	/**
	 * モバイルの端末情報取得クラスを作成する。
	 */
	public static function create(){
		$info = null;
		
		// 各端末のインスタンス作成処理を行う。
		$info = Clay_Mobile_Docomo::create($info);
		$info = Clay_Mobile_Ezweb::create($info);
		$info = Clay_Mobile_Softbank::create($info);
		$info = Clay_Mobile_Emobile::create($info);
		$info = Clay_Mobile_Willcom::create($info);
		$info = Clay_Mobile_Apple::create($info);
		$info = Clay_Mobile_Android::create($info);
		$info = Clay_Mobile_WindowsMobile::create($info);
		$info = Clay_Mobile_BlackBerry::create($info);
		
		// いずれにも該当しない場合にはこのクラスのインスタンスをPC用として作成
		if($info == null){
			$info = new Clay_Mobile();
		}
		return $info;
	}
	
	public function __construct(){
		$this->isMobile = false;
		$this->isFuturePhone = false;
		$this->isSmartPhone = false;
		$this->deviceType = "PC";
		$this->mobileId = "";
		// PCの場合は画面サイズを考慮しない
		$this->screenWidth = 0;
		$this->screenHeight = 0;
	}
	
	public function isMobile(){
		return $this->isMobile;
	}

	public function isFuturePhone(){
		return $this->isFuturePhone;
	}

	public function isSmartPhone(){
		return $this->isSmartPhone;
	}

	public function getDeviceType(){
		return $this->deviceType;
	}

	public function getMobileId(){
		return $this->mobileId;
	}

	public function getScreenWidth(){
		return $this->screenWidth;
	}

	public function getScreenHeight(){
		return $this->screenHeight;
	}
}
 