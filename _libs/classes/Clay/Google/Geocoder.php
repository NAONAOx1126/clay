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
 * GoogleのGeocoderAPI接続用のコネクションを管理するためのクラスです。
 *
 * @package Database
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Google_Geocoder{
	private $addressData;
	
	public function __construct($address, $language = "en"){
		// ベースURLを取得
		$baseUrl = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=";
		
		// 追加ヘッダ情報を設定
		$options = array("http" => array("header" => "Accept-Language: ".$language));
		$context = stream_context_create($options);
		
		// コンテンツを取得
		if(preg_match("/^([^0-9]+)([0-9]+)?([^0-9]*[0-9]+)?([^0-9]*[0-9]+)?/", mb_convert_kana($address, "n"), $p) > 0){
			$address = $p[0];
		}
		echo "URL : ".$baseUrl.urlencode($address)."<br>";
		$result = file_get_contents($baseUrl.urlencode($address), false, $context);
		
		// JSONデコードする。
		$this->addressData = json_decode($result);
	}
	
	public function getAddresses(){
		if($this->addressData->status == "OK"){
			return count($this->addressData->results);
		}else{
			return 0;
		}
	}
	
	public function getAddressData($index){
		if($index < $this->getAddresses()){
			return $this->addressData->results[$index];
		}
		return null;
	}
	
	public function getFormattedAddresses(){
		if($this->getAddresses()){
			$results = array();
			foreach($this->addressData->results as $result){
				$results[] = $result->formatted_address;
			}
			return $results;
		}
		return array();
	}
}
 