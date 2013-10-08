<?php
/**
 * Copyright (C) 2012 Clay System All Rights Reserved.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Clay System
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */
 
/**
 * Willcomの携帯端末情報を取得するためのクラスです。
 *
 * @package Mobile
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Mobile_Willcom extends Clay_Mobile{
	/**
	 * モバイルの端末情報取得クラスを作成する。
	 */
	public static function create($info){
		if($info == null){
			if(preg_match("/(DDIPOCKET|WILLCOM)/i", $_SERVER["HTTP_USER_AGENT"]) > 0){
				return new Clay_Mobile_Willcom();
			}
		}
		return $info;
	}
	
	public function __construct(){
		$this->isMobile = true;
		$this->isFuturePhone = true;
		$this->isSmartPhone = false;
		$this->deviceType = "Willcom";
		$this->mobileId = "";
		$this->screenWidth = 0;
		$this->screenHeight = 0;
	}
}
 