<?php
/**
 * Copyright (C) 2012 Clay System All Rights Reserved.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Clay System
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   4.0.0
 */

class Clay_Refactor_Table{
	private $connection;
	
	public function __construct($connection){
		$this->connection = $connection;
	}
	
	public function refactor($table){
		// テーブル名を分解
		list($prefix, $table_name) = explode("_", $table, 2);
		
		// プラグインベースディレクトリを作成
		$filename = CLAY_PLUGINS_ROOT.DIRECTORY_SEPARATOR."clay_".$prefix.DIRECTORY_SEPARATOR;
		if(!is_dir($filename)){
			mkdir($filename);
		}
		// プラグインテーブルディレクトリを作成
		$filename .= "tables".DIRECTORY_SEPARATOR;
		if(!is_dir($filename)){
			mkdir($filename);
		}
		
		// テーブルクラスのファイル名を生成
		$names = explode("_", $table_name);
		$classname = "";
		foreach($names as $name){
			$classname .= strtoupper(substr($name, 0, 1)).strtolower(substr($name, 1));
		}
		$classname .= "Table";
		$filename .= $classname.".php";
		
		// テーブルのクラスのファイルを開く
		if(($fp = fopen($filename, "w+")) !== FALSE){
			fwrite($fp, "<?php\r\n");
			fwrite($fp, "/**\r\n");
			fwrite($fp, " * Copyright (C) 2012 Clay System All Rights Reserved.\r\n");
			fwrite($fp, " * \r\n");
			fwrite($fp, " * Licensed under the Apache License, Version 2.0 (the \"License\");\r\n");
			fwrite($fp, " * you may not use this file except in compliance with the License.\r\n");
			fwrite($fp, " * You may obtain a copy of the License at\r\n");
			fwrite($fp, " * \r\n");
			fwrite($fp, " * http://www.apache.org/licenses/LICENSE-2.0\r\n");
			fwrite($fp, " * \r\n");
			fwrite($fp, " * Unless required by applicable law or agreed to in writing, software\r\n");
			fwrite($fp, " * distributed under the License is distributed on an \"AS IS\" BASIS,\r\n");
			fwrite($fp, " * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.\r\n");
			fwrite($fp, " * See the License for the specific language governing permissions and\r\n");
			fwrite($fp, " * limitations under the License.\r\n");
			fwrite($fp, " *\r\n");
			fwrite($fp, " * @author    Naohisa Minagawa <info@clay-system.jp>\r\n");
			fwrite($fp, " * @copyright Copyright (c) 2013, Clay System\r\n");
			fwrite($fp, " * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0\r\n");
			fwrite($fp, " * @since PHP 5.3\r\n");
			fwrite($fp, " * @version   4.0.0\r\n");
			fwrite($fp, " */\r\n");
			fwrite($fp, "/**\r\n");
			fwrite($fp, " * ".$table."テーブルの定義クラスです。\r\n");
			fwrite($fp, " */\r\n");
			$basename = strtoupper(substr($prefix, 0, 1)).strtolower(substr($prefix, 1));
			fwrite($fp, "class ".$basename."_".$classname." extends Clay_Plugin_Table{\r\n");
			fwrite($fp, "    /**\r\n");
			fwrite($fp, "     * コンストラクタです。\r\n");
			fwrite($fp, "     */\r\n");
			fwrite($fp, "    public function __construct(){\r\n");
			fwrite($fp, "        \$this->db = Clay_Database_Factory::getConnection(\"".$prefix."\");\r\n");
			fwrite($fp, "        parent::__construct(\"".$table."\", \"".$prefix."\");\r\n");
			fwrite($fp, "    }\r\n");
			fwrite($fp, "    /**\r\n");
			fwrite($fp, "     * テーブルを作成するためのスタティックメソッドです。。\r\n");
			fwrite($fp, "     */\r\n");
			$result = $this->connection->query("SHOW CREATE TABLE `".$table."`");
			$ctable = $result->fetch();
			foreach(array_keys($ctable) as $key){
				$create_table = $ctable[$key];
			}
			$create_table = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $create_table);
			fwrite($fp, "    public static function install(){\r\n");
			fwrite($fp, "        \$connection = Clay_Database_Factory::getConnection(\"".$prefix."\");\r\n");
			fwrite($fp, "        \$connection->query(\"".$create_table."\");\r\n");
			fwrite($fp, "    }\r\n");
			fwrite($fp, "}\r\n");
			fclose($fp);
		}
	}
}