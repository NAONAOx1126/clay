<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");

class Members_Twitter_ImportImage extends FrameworkModule{
	function execute($params){
		if(!isset($_SESSION["INPUT_DATA"][$params->get("key", "profile_image")]) && !empty($_SESSION[TWITTER_SESSION_KEY])){
			$image = $_SESSION[TWITTER_SESSION_KEY]->original_image_url;
			if(preg_match("/default_profile_[0-9]+\\.png$/", $image) > 0){
				$image = $_SESSION[TWITTER_SESSION_KEY]->profile_image_url;
			}
			$extension = substr($image, strrpos($image, "."));
			$filename = uniqid($_SESSION[TWITTER_SESSION_KEY]->id).$extension;
			$upload_file = MODULE_HOME.$_SERVER["USER_TEMPLATE"].DS."upload/".$filename;
			$upload_url = DS."upload/".$filename;
			if($params->check("trimx") || $params->check("trimy")){
				$image = new ImageConverter($image);
				$image->resizeShort($params->get("trimx", "0"), $params->get("trimy", "0"), false);
				$image->trim($params->get("trimx", "0"), $params->get("trimy", "0"));
				$image->save($upload_file);
			}else{
				$fp = fopen($upload_file, "w+");
				fwrite($fp, file_get_contents($params->get("file")));
				fclose($fp);
			}
			chmod($upload_file, 0644);
			$_SESSION["INPUT_DATA"][$params->get("key", "profile_image")] = $upload_url;
		}
		$_SERVER["INPUT_DATA"] = $_SESSION["INPUT_DATA"];
	}
}
?>