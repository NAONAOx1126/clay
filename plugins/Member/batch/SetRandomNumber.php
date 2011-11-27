<?php
// パラメータを変数に格納
$randomTo = $argv[0];

// この機能で使用するモデルクラス
LoadModel("CustomerModel", "Members");
LoadModel("CustomerOptionModel", "Members");

// 全てのデータにランダムな数値を設定する。
$model = new CustomerModel();
$result = $model->findAllBy(array());
$customers = array();
foreach($result as $data){
	$customers[] = $data->customer_id;
}
$model = new CustomerOptionModel();
$result = $model->findAllByName($randomTo);
$randoms = array();
foreach($result as $data){
	$randoms[$data->customer_id] = $data;
}

foreach($customers as $customer_id){
	// ランダムデータが存在しない場合は新規作成
	if(!isset($randoms[$customer_id])){
		$randoms[$customer_id] = new CustomerOptionModel(array("customer_id" => $customer_id, "option_name" => $randomTo));
	}
	
	// Likeカウントを設定
	$randoms[$customer_id]->option_value = mt_rand();
	
	// データを保存
	$db = DBFactory::getLocal();
	$db->beginTransaction();
	try{
		$randoms[$customer_id]->save($db);
		$db->commit();
	}catch(Exception $e){
		$db->rollback();
	}
}
?>
