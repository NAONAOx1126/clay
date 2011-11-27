<?php
// この機能で使用するモデルクラス
$memberPluginLoader = new PluginLoader("Member");
$memberPluginLoader->LoadModel("Setting");
$memberPluginLoader->LoadModel("CustomerModel");
$memberPluginLoader->LoadModel("CustomerTypeModel");
$memberPluginLoader->LoadModel("CustomerOptionModel");

/**
 * 携帯の個体番号でのログインを実行するモジュールです。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_Login_Mobile extends FrameworkModule{
	function execute($params){
		// アクセスしてきたモバイルIDを取得
		if(!empty($_SERVER["HTTP_X_DCMGUID"])){
			// ドコモID(GUID対応)
			$mobileId = $_SERVER["HTTP_X_DCMGUID"];
		}elseif(!empty($_SERVER["HTTP_X_UP_SUBNO"])){
			// au端末の場合
			$mobileId = $_SERVER["HTTP_X_UP_SUBNO"];
		}elseif(!empty($_SERVER["HTTP_X_JPHONE_UID"])){
			// Softbank端末の場合
			$mobileId = $_SERVER["HTTP_X_JPHONE_UID"];
		}elseif(preg_match("/^.+ser([0-9a-zA-Z]+).*$/", $_SERVER["HTTP_USER_AGENT"], $ua) > 0){
			// ドコモのユーザーエージェントから取得
			$mobileId = $ua[1];
		}elseif(preg_match("/^.+\/SN([0-9a-zA-Z]+).*$/", $_SERVER["HTTP_USER_AGENT"], $ua) > 0){
			// Softbankのユーザーエージェントから取得
			$mobileId = $ua[1];
		}else{
			$mobileId = $_SESSION["MOBILE_GUID"];
		}
		
		// モバイルIDが設定されておらず、guidパラメータも未設定の場合はguid付きでリダイレクトする。
		if(empty($mobileId)){
			if(empty($_POST["guid"])){
				header("Location: ".((strpos($_SERVER["REQUEST_URI"], "?") > 0)?$_SERVER["REQUEST_URI"]."&guid=ON":$_SERVER["REQUEST_URI"]."?guid=ON"));
				exit;
			}
			$mobileId = "Mozilla/PC-ACCESS:TEST-USE";
		}
		
		// モバイルのGUIDをセッションに保存
		$_SESSION["MOBILE_GUID"] = $mobileId;
		
		// モバイルIDが渡った場合にはユーザ情報を取得する。
		if(!empty($mobileId)){
			// カスタマモデルを使用して顧客情報を取得
			$customer = new CustomerModel();
			$customer->findByMobileId($mobileId);
			
			if(empty($customer->customer_id)){
				// 該当するデータが無い場合はデータを作成
				if($params->get("auto", "0") == "1"){
					// トランザクションデータベースの取得
					$db = DBFactory::getLocal();// トランザクションの開始
					$db->beginTransaction();
					
					try{
						// データを登録する。
						$customer->mobile_id = $mobileId;
						$customer->save($db);
						
						// エラーが無かった場合、処理をコミットする。
						$db->commit();
					}catch(Exception $ex){
						unset($_POST["regist"]);
						$db->rollBack();
						throw $ex;
					}
					
					// 再度モバイルIDで検索
					$customer->findByMobileId($mobileId);
				}
			}
			if(!empty($customer->customer_id)){
				$_SESSION[CUSTOMER_SESSION_KEY] = $customer;
				$customerType = new CustomerTypeModel();
				$_SESSION[CUSTOMER_SESSION_KEY]->types = $customerType->findAllByCustomer($customer->customer_id);
				$customerOption = new CustomerOptionModel();
				$customerOptions = $customerOption->getOptionArrayByCustomer($customer->customer_id);
				foreach($customerOptions as $name => $option){
					$_SESSION[CUSTOMER_SESSION_KEY]->$name = $option->option_value;
				}
			}
		}
	
		if(empty($_SESSION[CUSTOMER_SESSION_KEY])){
			if($params->get("error")){
				throw new InvalidException(array("ログインに失敗しました"));
			}elseif($params->get("redirect")){
				throw new RedirectException();
			}
		}
		print_r($_SESSION[CUSTOMER_SESSION_KEY]);
		$_SERVER["ATTRIBUTES"][$params->get("result", "customer")] = $_SESSION[CUSTOMER_SESSION_KEY];
	}
}
?>