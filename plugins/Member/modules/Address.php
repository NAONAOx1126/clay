<?php
LoadModel("Setting", "Members");

class Members_Address extends FrameworkModule{
	function execute($param){
		$mode = $param->get("zip", "zip");
		$zip1 = $param->get("zip1", "zip1");
		$zip2 = $param->get("zip1", "zip2");
		$pref = $param->get("pref", "pref");
		$address1 = $param->get("address1", "address1");
		
		// 注文者郵便番号検索
		if(!empty($_POST[$mode])){
			// 入力エラーチェック
			$errors = array();
			if(preg_match("/^[0-9]{3}-[0-9]{4}$/", $_POST[$zip1]."-".$_POST[$zip2]) == 0){
				throw new IllegalException(array(SHOPPING_MESSAGE_ILLEGAL_ZIP));
			}
			
			// 郵便番号のデータを取得する。
			$zips = new ZipsTable();
			$prefs = new PrefsTable();

			// クエリの構築
			$select = new DatabaseSelect($zips);
			$select->addColumn($prefs->id)->addColumn($zips->city)->addColumn($zips->town)->addColumn($zips->flg3);
			$select->joinInner($prefs, array($zips->state." = ".$prefs->name));
			$select->addWhere($zips->zipcode." = ?", array($_POST[$zip1].$_POST[$zip2]));
			$result = $select->execute();
			
			if(count($result) > 0){
				$_POST[$pref] = $result[0]["id"];
				$_POST[$address1] = $result[0]["city"].(($result[0]["flg3"] == "1")?$result[0]["town"]:"");
			}
			
			// POSTの内容をcustomerに設定
			foreach($_POST as $name => $value){
				$_SESSION[$customerSessionKey][$name] = $value;
			}
		}
	}
}
?>
