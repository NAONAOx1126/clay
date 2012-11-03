<?php
// 共通のライブラリの呼び出し。
$_SERVER["CONFIGURE"]->SESSION_MANAGER = "";
require_once(dirname(__FILE__)."/../../libs/common/require.php");

// パラメータを変数に格納
$_SERVER["SERVER_NAME"] = $argv[1];
if($argc > 2){
	$config_name = $argv[2];
}else{
	$configure_name = "default";
}

ini_set("memory_limit", -1);

set_time_limit(0);


// データベースに接続する。
$connection = DBFactory::getConnection($config_name);

// データベースからテーブルのリストを取得する。
$prepare = $connection->prepare("SHOW TABLES");
$prepare->execute($values);
while($row = $prepare->fetch(PDO::FETCH_NUM)){
	// テーブル名と対応するパッケージ名／クラス名を取得する。
	$tableName = $row[0];
	list($packageCode, $classCode) = explode("_", $tableName, 2);
	$packageName = strtoupper(substr($packageCode, 0, 1)).strtolower(substr($packageCode, 1));
	$className = "";
	$splited = explode("_", $classCode);
	foreach($splited as $item){
		$className .= strtoupper(substr($item, 0, 1)).strtolower(substr($item, 1));
	}
	$className .= "Table";
	
	// パッケージディレクトリが無い場合作成する。
	$packagePath = FRAMEWORK_PLUGIN_HOME."/".$packageName."/tables";
	if(!is_dir($packagePath)){
		echo "CREATE : ".$packagePath."\r\n";
		mkdir($packagePath);
	}
	
	// テーブルクラスファイルが無い場合作成する。
	$filePath = $packagePath."/".$className.".php";
	// ソースデータを生成
	$source = "<?php\r\n";
	$source .= 'class '.$packageName.'_'.$className.' extends Clay_Plugin_Table{'."\r\n";
	$source .= "\t".'function __construct(){'."\r\n";
	$source .= "\t\t".'$this->db = DBFactory::getConnection("'.$packageCode.'");'."\r\n";
	$source .= "\t\t".'parent::__construct("'.$tableName.'", "'.$packageCode.'");'."\r\n";
	$source .= "\t".'}'."\r\n";
	$source .= '}'."\r\n";
	$source .= "?>\r\n";
	
	// ファイルに書き込み
	echo "CREATE : ".$filePath."\r\n";
	if(($fp = fopen($filePath, "w+"))){
		fwrite($fp, $source);
		fclose($fp);
	}
}
$prepare->closeCursor();
?>
