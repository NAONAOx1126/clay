<?php
/**
 * ### Base.Forms.SplitColumns
 * カラムを分割するクラスです。
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
 * @param target 対象とするキー
 * @param regex クリアする対象の変数
 * @param result 分割対象のカラムリスト
 */
class Base_Forms_SplitColumns extends FrameworkModule{
	function execute($params){
		if(isset($_SERVER["ATTRIBUTES"][$params->get("key")]) && is_array($_SERVER["ATTRIBUTES"][$params->get("key")])){
			foreach($_SERVER["ATTRIBUTES"][$params->get("key")] as $index => $data){
				if(preg_match("/".$params->get("regex")."/", $data[$params->get("target")], $p)){
					$columns = explode(",", $params->get("result"));
					foreach($p as $i => $param){
						$_SERVER["ATTRIBUTES"][$params->get("key")][$index][$columns[$i]] = $param;
					}
				}
			}
		}
	}
}
?>
