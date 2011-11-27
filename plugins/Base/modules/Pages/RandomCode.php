<?php
/**
 * ランダムな英数からなるコードを発行します。一意性は保証されません。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Base
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */

class Base_Pages_RandomCode extends FrameworkModule{
	function execute($params){
		if($params->check("code")){
			$result = "";
			$codes = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
			for($i = 0; $i < $params->get("length", "20"); $i ++){
				$result .= substr($codes, mt_rand(0, strlen($codes) - 1), 1);
			}
			$_POST[$params->get("code")] = $result;
		}
	}
}
?>
