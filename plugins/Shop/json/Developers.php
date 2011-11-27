<?php
// ショッピングカートの設定を取得
LoadModel("ShoppingSettings", "Shopping");

// この処理で使用するテーブルモデルをインクルード
LoadTable("DevelopersTable", "Shopping");

// メーカーのリストを取得
$developers = new DevelopersTable();
$select = new DatabaseSelect($developers);
$select->addColumn($developers->_W);
$select->addWhere($developers->developer_name." LIKE ?", array("%".trim($_POST["name"])."%"));
$result = $select->execute();
?>
