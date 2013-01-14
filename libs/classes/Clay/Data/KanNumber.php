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
 * 漢数字を扱うためのクラスです。。
 *
 * @package Data
 * @author Naohisa Minagawa <info@clay-system.jp>
 */
class Clay_Data_KanNumber{
	private $text;
	
	private $kanNumbers = array();
	
	private $numberMaps = array();
	
	public function __construct($text){
		$this->text = $text;
		$index = 0;
		for($pos = 0; $pos < mb_strlen($text); $pos ++){
			$char = mb_substr($text, $pos, 1);
			$kans = "〇一二三四五六七八九十百千";
			$kanAdds = "万億兆京";
			if(mb_strpos($kans, $char) !== FALSE){
				if(!isset($this->kanNumbers[$index])){
					$this->kanNumbers[$index] = "";
				}
				$this->kanNumbers[$index] .= $char;
			}elseif(mb_strpos($kanAdds, $char) !== FALSE && !empty($this->kanNumbers[$index])){
				$this->kanNumbers[$index] .= $char;
			}elseif(!empty($this->kanNumbers[$index])){
				$index ++;
			}
		}
		foreach($this->kanNumbers as $kanNumber){
			$number = 0;
			$fixedNumber = 0;
			for($pos = 0; $pos < mb_strlen($kanNumber); $pos ++){
				$char = mb_substr($kanNumber, $pos, 1);
				$kans = "〇一二三四五六七八九";
				$kanMids = "十百千";
				$kanAdds = "万億兆京";
				if(($num = mb_strpos($kans, $char)) !== FALSE){
					$number = $number * 10 + $num;
				}elseif(($num = mb_strpos($kanMids, $char)) !== FALSE){
					$fixedNumber += ((($number > 0)?$number:1) * pow(10, $num + 1));
					$number = 0;
				}elseif(($num = mb_strpos($kanAdds, $char)) !== FALSE){
					$fixedNumber += $number;
					$number = $fixedNumber % 10000;
					$fixedNumber -= $number;
					$fixedNumber += $number * pow(10000, $num + 1);
					$number = 0;
				}
			}
			$this->numberMaps[$kanNumber] = $fixedNumber + $number;
		}
	}
	
	public function getConvertedText(){
		$text = $this->text;
		foreach($this->numberMaps as $kan => $alb){
			$text = str_replace($kan, $alb, $text);
		}
		return $text;
	}
}
 