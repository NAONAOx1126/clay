<?php
/**
 * ### Base.Site.List
 * サイトデータのリストを取得する。
 */
class Base_Site_List extends FrameworkModule{
	function execute($params){
		// サイトデータを取得する。
		$loader = new PluginLoader();
		$site = $loader->loadModel("SiteModel");
		$sites = $site->findAllBy(array());
		
		$_SERVER["ATTRIBUTES"][$params->get("result", "sites")] = $sites;
	}
}
?>
