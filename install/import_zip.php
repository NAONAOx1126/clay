<?php
// 共通のライブラリの呼び出し。
include_once("../common/require.php");

// 最大実行時間を無制限に変更
ini_set("max_execution_time", 0);

// トランザクションデータベースの取得
Clay_Database_Factory::begin();// トランザクションの開始

try{
	// プラグインのローダーの読み込み
	$loader = new Clay_Plugin();
	
	// 郵便番号仮テーブルを読み込み
	$zipTemps = $loader->loadTable("ZipTempsTable");
	
	// 郵便番号テーブルモデルの読み込み
	$zips = $loader->loadTable("ZipsTable");
	
	// 郵便番号テーブルモデルの読み込み
	$prefs = $loader->loadTable("PrefsTable");
	
	echo "BATCH START : ".time()."<br>\r\n";

	// 郵便番号仮テーブルの内容破棄
	$truncate = new Clay_Query_Truncate($zipTemps);
	$truncate->execute();
	
	echo "TEMP DELETED : ".time()."<br>\r\n";
	
	// CSVファイルを読み込む
	if(($fp = fopen(FRAMEWORK_HOME."/install/csvs/KEN_ALL.CSV", "r")) !== FALSE){
		$insert = new Clay_Query_Insert($zipTemps);
		while(($line = fgets($fp)) !== FALSE){
			// CSVの内容をDBに登録する。
			$data = explode(",", str_replace("\"", "", trim(mb_convert_encoding($line, "UTF-8", "Shift_JIS"))));
			$sqlval = array();
			$sqlval["code"] = $data[0];
			$sqlval["old_zipcode"] = $data[1];
			$sqlval["zipcode"] = $data[2];
			$sqlval["state_kana"] = $data[3];
			$sqlval["city_kana"] = $data[4];
			$sqlval["town_kana"] = $data[5];
			$sqlval["state"] = $data[6];
			$sqlval["city"] = $data[7];
			$sqlval["town"] = $data[8];
			$sqlval["flg1"] = $data[9];
			$sqlval["flg2"] = $data[10];
			$sqlval["flg3"] = $data[11];
			$sqlval["flg4"] = $data[12];
			$sqlval["flg5"] = $data[13];
			$sqlval["flg6"] = $data[14];
			$result = $insert->execute($sqlval);
		}
	}
	
	echo "TEMP INSERTED : ".time()."<br>\r\n";

	// 本番データの削除
	$truncate = new Clay_Query_Truncate($zips);
	$truncate->execute();
	
	echo "DATA DELETED : ".time()."<br>\r\n";
	
	// 一時データを本番データに反映
	$insert = new Clay_Query_Insert($zips);
	$select = new Clay_Query_Select($zipTemps);
	$select->addColumn($zipTemps->_W);
	$insert->copy($select);
	
	echo "DATA INSERTED : ".time()."<br>\r\n";

	// 都道府県データの削除
	$truncate = new Clay_Query_Truncate($prefs);
	$truncate->execute();
	
	echo "PREF DELETED : ".time()."<br>\r\n";
	
	// 都道府県データを郵便番号データから自動生成
	$insert = new Clay_Query_Insert($prefs);
	$select = new Clay_Query_Select($zips);
	$select->addColumn("SUBSTRING(".$zips->code.", 1, 2)");
	$select->addColumn($zips->state);
	$select->addWhere($zips->flg3." = 0");
	$select->addGroupBy($zips->state);
	$select->addOrder($zips->code);
	$insert->copy($select, array("id", "name"));
	
	// エラーが無かった場合、処理をコミットする。
	Clay_Database_Factory::commit();
	
	echo "BATCH FINISHED : ".time()."<br>\r\n";
}catch(Clay_Exception_Database $e){
	Clay_Database_Factory::rollBack();
	print_r($e);
}
?>
