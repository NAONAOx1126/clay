<?php
// この機能で使用するモデルクラス
LoadModel("Setting", "Members");
LoadModel("CustomerOptionModel", "Members");

/**
 * 携帯の個体番号でのログインを実行するモジュールです。
 *
 * @params error ログイン失敗時に遷移するページのテンプレートパス
 * @params redirect ログイン成功時にリダイレクトするURL
 * @params session 顧客情報を保存するセッション名
 * @params auto 1を設定すると、携帯の個体番号が渡っていた場合、自動でユーザー情報を作成する
 * @params result 顧客情報をページで使うためのキー名
 */
class Members_CreateSerial extends FrameworkModule{
	function execute($params){
		if($params->check("option")){
			// シリアル用のオプションキーを取得
			$optionKey = $params->get("option");
			
			// 既にシリアルを発行している場合には、何もしない
			if(!empty($_SESSION[CUSTOMER_SESSION_KEY]->customer_id) && empty($_SESSION[CUSTOMER_SESSION_KEY]->$optionKey)){
				// 顧客IDを取得
				$customerId = $_SESSION[CUSTOMER_SESSION_KEY]->customer_id;					
	
				// シリアルを変換するためのキーチェインを生成
				$keyChain = array("7", "j", "W", "H", "Z", "0", "r", "n", "y", "6", "C", "x", "D", "I", "X", "8", "p", "F", "h", "e", "t", "s", "k", "9", "E", "b", "T", "f", "i", "M", "V", "O", "5", "g", "z", "o", "v", "Q", "1", "4", "a", "U", "w", "A", "d", "N", "q", "K", "2", "3", "G", "B", "R", "m", "l", "L", "J", "Y", "P", "u", "S", "c");
			
				if($params->check("hash")){
					// 顧客IDとユニークIDでハッシュを生成
					$hash = sha1($customerId.":".uniqid());
				}else{
					$hash = sprintf("%05d", mt_rand(0, 99999)).substr(uniqid(), 5);
				}
					
				// キーチェインインデックス／ソルトインデックスを初期化
				$saltIndex = 0;
				$index = 0;
				
				// シリアルをキーチェインから取得
				$serial = "";
				for($saltIndex = 0; $saltIndex < strlen($hash); $saltIndex ++){
					$salt = substr($hash, $saltIndex, 1);
					
					switch($salt){
						case "0": $index += 1; break;
						case "1": $index += 2; break;
						case "2": $index += 3; break;
						case "3": $index += 4; break;
						case "4": $index += 5; break;
						case "5": $index += 6; break;
						case "6": $index += 7; break;
						case "7": $index += 8; break;
						case "8": $index += 9; break;
						case "9": $index += 10; break;
						case "a": $index += 11; break;
						case "b": $index += 12; break;
						case "c": $index += 13; break;
						case "d": $index += 14; break;
						case "e": $index += 15; break;
						case "f": $index += 16; break;
					}
					
					if(count($keyChain) <= $index){
						$index -= count($keyChain);
					}
					
					$serial .= $keyChain[$index];
				}

				// トランザクションデータベースの取得
				$db = DBFactory::getLocal();// トランザクションの開始
				$db->beginTransaction();
				
				try{
					// 顧客データモデルを初期化
					$values = array();
					$option = new CustomerOptionModel(array("customer_id" => $customerId, "option_name" => $optionKey));
					$option->findByPrimaryKey($customerId, $optionKey);
					$option->option_value = $serial;
					
					// 画像データを登録する。
					$option->save($db);
					
					// エラーが無かった場合、処理をコミットする。
					$db->commit();
	
					// 結果を登録する。
					$_SESSION[CUSTOMER_SESSION_KEY]->$optionKey = $serial;
					$_SERVER["ATTRIBUTES"][$params->get("result", "customer")]= $_SESSION[CUSTOMER_SESSION_KEY];					
				}catch(Exception $ex){
					$db->rollBack();
					throw $ex;
				}
			}
		}
	}
}
?>