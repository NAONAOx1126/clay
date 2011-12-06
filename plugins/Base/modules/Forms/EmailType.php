<?php
/**
 * ### Base.Forms.EmailType
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
 * @param target 対象とするメールアドレスのキー
 * @param result ドメイン照合結果を格納するカラム
 */
class Base_Forms_EmailType extends FrameworkModule{
	function execute($params){
		if(isset($_SERVER["ATTRIBUTES"][$params->get("key")]) && is_array($_SERVER["ATTRIBUTES"][$params->get("key")])){
			foreach($_SERVER["ATTRIBUTES"][$params->get("key")] as $index => $data){
				if(preg_match("/@(.+)$/", $data[$params->get("target")], $p)){
					// メールアドレスのドメインを取得する。
					$domain = $p[1];
					$loader = new PluginLoader();
					$mobileDomain = $loader->loadModel("MobileDomainModel");
					$mobileDomain->findByMobileDomain($domain);
					if($mobileDomain->mobile_domain_id > 0){
						$type = $mobileDomain->mobile_type;
					}else{
						$type = "PC";
					}
					$_SERVER["ATTRIBUTES"][$params->get("key")][$index][$params->get("result")] = $type;
				}
			}
		}
	}
}
?>
