<?php
/**
 * ### Base.Forms.MergeColumns
 * カラムを結合するクラスです。
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
class Base_Forms_MergeColumns extends FrameworkModule{
	function execute($params){
		if(isset($_SERVER["ATTRIBUTES"][$params->get("key")]) && is_array($_SERVER["ATTRIBUTES"][$params->get("key")])){
			foreach($_SERVER["ATTRIBUTES"][$params->get("key")] as $index => $data){
				$columns = explode(",", $params->get("target"));
				$value = "";
				foreach($columns as $i => $column){
					if($i > 0){
						$value .= $params->get("delimiter");
					}
					$value .= $data[$column];
				}
				$_SERVER["ATTRIBUTES"][$params->get("key")][$index][$params->get("result")] = $value;
			}
		}
	}
}
?>
