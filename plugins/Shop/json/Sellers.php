<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("SellersTable", "Shopping");

// メーカーのリストを取得
$sellers = new SellersTable();
$select = new DatabaseSelect($sellers);
$select->addColumn($sellers->_W);
$select->addWhere($sellers->seller_name." LIKE ?", array("%".trim($_POST["name"])."%"));
$result = $select->execute();
?>
