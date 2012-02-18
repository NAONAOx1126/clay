<?php
/**
 * ### Shop.Summery.MergeAreas
 * 受注データでのサマリを取得する。
 * @param pref 都道府県のキー
 * @param area エリアのキー
 * @param data データのキー
 */
class Shop_Summery_MergeAreas extends FrameworkModule{
	function execute($params){
		$pref = $params->get("pref");
		$area = $params->get("area");
		if(is_array($_SERVER["ATTRIBUTES"][$params->get("data")])){
			foreach($_SERVER["ATTRIBUTES"][$params->get("data")] as $index => $data){
				$_SERVER["ATTRIBUTES"][$params->get("data")][$index]->$area = $this->getArea($_SERVER["ATTRIBUTES"][$params->get("data")][$index]->$pref);
				$_SERVER["ATTRIBUTES"][$params->get("data")."_new"][$index]->$area = $this->getArea($_SERVER["ATTRIBUTES"][$params->get("data")."_new"][$index]->$pref);
				$_SERVER["ATTRIBUTES"][$params->get("data")."_repeat"][$index]->$area = $this->getArea($_SERVER["ATTRIBUTES"][$params->get("data")."_repeat"][$index]->$pref);
			}
		}else{
			$_SERVER["ATTRIBUTES"][$params->get("data")]->$area = $this->getArea($_SERVER["ATTRIBUTES"][$params->get("data")]->$pref);
			$_SERVER["ATTRIBUTES"][$params->get("data")."_new"]->$area = $this->getArea($_SERVER["ATTRIBUTES"][$params->get("data")."_new"]->$pref);
			$_SERVER["ATTRIBUTES"][$params->get("data")."_repeat"]->$area = $this->getArea($_SERVER["ATTRIBUTES"][$params->get("data")."_repeat"]->$pref);
		}
	}
	
	function getArea($pref){
		switch($pref){
			case "北海道":
				return "北海道";
			case "青森県":
			case "岩手県":
			case "宮城県":
			case "秋田県":
			case "山形県":
			case "福島県":
				return "東北";
			case "茨城県":
			case "栃木県":
			case "群馬県":
			case "埼玉県":
			case "千葉県":
			case "東京都":
			case "神奈川県":
				return "関東";
			case "新潟県":
			case "富山県":
			case "石川県":
			case "福井県":
			case "山梨県":
			case "長野県":
			case "岐阜県":
			case "静岡県":
			case "愛知県":
				return "中部";
			case "三重県":
			case "滋賀県":
			case "京都府":
			case "大阪府":
			case "兵庫県":
			case "奈良県":
			case "和歌山県":
				return "近畿";
			case "鳥取県":
			case "島根県":
			case "岡山県":
			case "広島県":
			case "山口県":
			case "徳島県":
			case "香川県":
			case "愛媛県":
			case "高知県":
				return "中国・四国";
			case "福岡県":
			case "佐賀県":
			case "長崎県":
			case "熊本県":
			case "大分県":
			case "宮崎県":
			case "鹿児島県":
			case "沖縄県":
				return "九州";
			default:
				return "該当なし";			
		}
	}
}
?>
