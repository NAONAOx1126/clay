<?php
/**
 * ### Base.Site.Reload
 * サイトのキャッシュファイルをクリアし、再生成させる。
 */
class Base_Site_Reload extends FrameworkModule{
	function execute($params){
		// サイトデータのキャッシュを削除する。
		foreach(glob(FRAMEWORK_HOME."/cache/*/site_configure.php") as $filename){
			unlink($filename);
		}
	}
}
?>
