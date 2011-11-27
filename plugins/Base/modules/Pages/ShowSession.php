<?php
/**
 * 未処理のエラーがある場合にはエラーを再発行するための基本クラスです。
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

class Base_Pages_ShowSession extends FrameworkModule{
	function execute($params){
		if($params->check("session") && $params->check("attr")){
			$sessions = explode(".", $params->get("session"));
			$attr = $params->get("attr");
			
			$s = $_SESSION;
			foreach($sessions as $name){
				$s = (array) $s[$name];
			}
			foreach($s as $name => $value){
				$_SERVER["ATTRIBUTES"][$attr][$name] = $value;
			}
			print_r($_SERVER["ATTRIBUTES"][$attr][$name]);
		}
	}
}
?>
