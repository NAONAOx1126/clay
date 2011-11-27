<?php
class Members_UploadFile extends FrameworkModule{
	function execute($params){
		if(isset($_FILES[$params->get("key")])){
			$file = $_FILES[$params->get("key")];
			if ($file["error"] == UPLOAD_ERR_OK && is_uploaded_file($file["tmp_name"])) {
				if(!$params->check("max") || $file["size"] <= $params->get("max")){
					$extension = substr($file["name"], strrpos($file["name"], "."));
					$filename = uniqid($_POST[$params->get("prefix")]).$extension;
					$upload_file = MODULE_HOME.$_SERVER["USER_TEMPLATE"].DS."upload/".$filename;
					$upload_url = DS."upload/".$filename;
					if($params->check("trimx") || $params->check("trimy")){
						$image = new ImageConverter($file["tmp_name"]);
						$image->resizeShort($params->get("trimx", "0"), $params->get("trimy", "0"), false);
						$image->trim($params->get("trimx", "0"), $params->get("trimy", "0"));
						$image->save($upload_file);
						chmod($upload_file, 0644);
						$_POST[$params->get("key")] = $upload_url;
					}else{
						if (move_uploaded_file($file["tmp_name"], $upload_file)) {
							chmod($upload_file, 0644);
							$_POST[$params->get("key")] = $upload_url;
						}
					}
				}else{
					throw new InvalidException(array("アップロードするファイルは".number_format($params->get("max"))."バイト以下にしてください。"));
				}
			}else{
				if($file["error"] != UPLOAD_ERR_NO_FILE){
					throw new InvalidException(array("アップロードファイルが正しくありません。"));
				}
			}
		}else{
			throw new InvalidException(array("アップロードファイルが正しくありません。"));
		}
		if(!empty($_POST["upload"]) && $params->check("shift")){
			throw new ShiftException();
		}
	}
}
?>
