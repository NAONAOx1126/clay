<?php
class Base_Pages_SaveSiteImage extends FrameworkModule{
	function execute($params){
		if($params->check("key")){
			// 入力データからサイトのURLを取得する。
			$url = $_SERVER["INPUT_DATA"][$params->get("key")];
			$upload_file = MODULE_HOME.$_SERVER["USER_TEMPLATE"].DS."upload/".sha1($url).".jpg";
			$upload_url = MINES_URL_BASE.DS."upload/".sha1($url).".jpg";
			
			// サイトのサムネイル作成用の処理
			$command = MINES_HOME."/bin/";
			$command .= (isset($_SERVER["CONFIGURE"]["GLOBAL"]["OS"])?$_SERVER["CONFIGURE"]["GLOBAL"]["OS"]:"i386");
			$command .= "/wkhtmltoimage --crop-h 1280 --javascript-delay 2000 \"".$url."\" \"".$upload_file."\"";
			exec($command." 2> /dev/null > /dev/null &", $out, $ret);
			$_SERVER["INPUT_DATA"][$params->get("key")."_image"] = $_SESSION["INPUT_DATA"][$params->get("key")."_image"] = $upload_url;
		}else{
			$_SERVER["INPUT_DATA"][$params->get("key")."_image"] = $_SESSION["INPUT_DATA"][$params->get("key")."_image"] = $params->get("default", "");
		}
	}
}
?>
