<?php
LoadTable("PrefsTable");

class Base_Selections_Prefectures extends FrameworkModule{
	function execute($params){
		if(empty($_SERVER["ATTRIBUTES"]["SELECTION"]["prefectures"])){
			// 都道府県のプルダウン用リストを生成
			$prefs = new PrefsTable();

			$select = new DatabaseSelect($prefs);
		
			$select->addColumn($prefs->_W);
			$result = $select->execute();
				
			// IDをキーとする連想配列を構築
			foreach($result as $row){
				$_SERVER["ATTRIBUTES"]["SELECTION"]["prefectures"][$row["id"]] = $row["name"];
			}
		}
	}
}
?>
