<?php
/**
 * ### Base.Forms.MergeDateColumns
 * 日付形式としてテキストを結合するクラスです。
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
class Base_Forms_MergeDateColumns extends FrameworkModule{
	function execute($params){
		$_POST[$params->get("result")] = "";
		if($params->check("year") && isset($_POST[$params->get("year")]) && is_numeric($_POST[$params->get("year")])){
			$_POST[$params->get("result")] .= sprintf("%04d", $_POST[$params->get("year")]);
		}
		if($params->check("month") && isset($_POST[$params->get("month")]) && is_numeric($_POST[$params->get("month")])){
			if(!empty($_POST[$params->get("result")])){
				$_POST[$params->get("result")] .= "-";
			}
			$_POST[$params->get("result")] .= sprintf("%02d", $_POST[$params->get("month")]);
		}
		if($params->check("day") && isset($_POST[$params->get("day")]) && is_numeric($_POST[$params->get("day")])){
			if(!empty($_POST[$params->get("result")])){
				$_POST[$params->get("result")] .= "-";
			}
			$_POST[$params->get("result")] .= sprintf("%02d", $_POST[$params->get("day")]);
		}
		if($params->check("hour") && isset($_POST[$params->get("hour")]) && is_numeric($_POST[$params->get("hour")])){
			if(!empty($_POST[$params->get("result")])){
				$_POST[$params->get("result")] .= " ";
			}
			$_POST[$params->get("result")] .= sprintf("%02d", $_POST[$params->get("hour")]);
		}
		if($params->check("minute") && isset($_POST[$params->get("minute")]) && is_numeric($_POST[$params->get("minute")])){
			if(!empty($_POST[$params->get("result")])){
				$_POST[$params->get("result")] .= ":";
			}
			$_POST[$params->get("result")] .= sprintf("%02d", $_POST[$params->get("minute")]);
		}
		if($params->check("second") && isset($_POST[$params->get("second")]) && is_numeric($_POST[$params->get("second")])){
			if(!empty($_POST[$params->get("result")])){
				$_POST[$params->get("result")] .= ":";
			}
			$_POST[$params->get("result")] .= sprintf("%02d", $_POST[$params->get("second")]);
		}
	}
}
?>
