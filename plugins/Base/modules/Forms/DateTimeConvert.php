<?php
/**
 * ### Base.Forms.DateTimeConvert
 * 日付の表示を変換するクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param key 変数のキー
 * @param target 対象とするカラムのリスト
 * @param delimiter 結合時に設定するデリミタ
 * @param result 結合後のカラム
 */
class Base_Forms_DateTimeConvert extends FrameworkModule{
	function execute($params){
		if(isset($_SERVER["ATTRIBUTES"][$params->get("key")]) && is_array($_SERVER["ATTRIBUTES"][$params->get("key")])){
			foreach($_SERVER["ATTRIBUTES"][$params->get("key")] as $index => $data){
				$srcSuffix = explode(",", $params->get("src_suffix"));
				$destSuffix = explode(",", $params->get("dest_suffix"));
				$regex = "";
				foreach($srcSuffix as $suffix){
					$regex .= "([0-9]+)".$suffix;
				}
				if(preg_match("/".$regex."/", $data[$params->get("target")], $p) > 0){
					$result = "";
					array_shift($p);
					foreach($p as $i => $value){
						if(count($destSuffix) <= $i) break;
						$result .= $value.$destSuffix[$i];
					}
					$_SERVER["ATTRIBUTES"][$params->get("key")][$index][$params->get("result")] = $result;
				}
			}
		}
	}
}
?>
