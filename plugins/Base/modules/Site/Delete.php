<?php
/**
 * ### Base.Site.Delete
 * サイトのデータを削除する。
 */
class Base_Site_Delete extends FrameworkModule{
	function execute($params){
		// サイトデータを取得する。
		$loader = new PluginLoader();
		$site = $loader->loadModel("SiteModel");
		$site->findByPrimaryKey($_POST["site_id"]);
		
		// トランザクションデータベースの取得
		$db = DBFactory::getConnection();
		
		// トランザクションの開始
		$db->beginTransaction();
		
		try{
			$site->delete($db);
					
			// エラーが無かった場合、処理をコミットする。
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}
	}
}
?>
