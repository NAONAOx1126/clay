<?php
/**
 * ### Base.Site.Save
 * サイトのデータを保存する。
 */
class Base_Site_Save extends FrameworkModule{
	function execute($params){
		// サイトデータを取得する。
		$loader = new PluginLoader();
		$site = $loader->loadModel("SiteModel");
		$site->findByPrimaryKey($_POST["site_id"]);
		foreach($_POST as $key => $value){
			$site->$key = $value;
		}
		
		// トランザクションデータベースの取得
		$db = DBFactory::getConnection();
		
		// トランザクションの開始
		$db->beginTransaction();
		
		if($site->site_code == ""){
			throw new InvalidException(array("サイトコードは必須です"));
		}

		$site2 = $loader->loadModel("SiteModel");
		$site2->findBySiteCode($site->site_code);
		if($site->site_id != $site2->site_id && $site->site_code == $site2->site_code){
			throw new InvalidException(array("サイトコードは重複できません"));
		}

		$site2->findByDomainName($site->domain_name);
		if($site->site_id != $site2->site_id && $site->domain_name == $site2->domain_name){
			throw new InvalidException(array("ドメイン名は重複できません"));
		}
		
		try{
			$site->save($db);
					
			// エラーが無かった場合、処理をコミットする。
			$db->commit();
		}catch(Exception $e){
			$db->rollBack();
			throw $e;
		}
	}
}
?>
