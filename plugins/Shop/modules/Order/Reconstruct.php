<?php
/**
 * ### Shop.Order.Import
 * 注文情報をインポートするためのクラスです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Modules
 * @package   Shop
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 * @param key インポートするファイルの形式を特定するためのキー
 */
class Shop_Order_Reconstruct extends FrameworkModule{
	function execute($params){
		// ローダーを初期化
		$loader = new PluginLoader("Shop");
		
		// リピート回数集計用のテーブルを再構築
		$model = $loader->loadModel("RepeaterOrderModel");
		$model->reconstruct();
		$model = $loader->loadModel("RepeaterOrderDetailModel");
		$model->reconstruct();
		$model = $loader->loadModel("RepeaterOrderPaymentModel");
		$model->reconstruct();
	}
}
?>
