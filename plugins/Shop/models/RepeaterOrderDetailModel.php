<?php
/**
 * 受注詳細のデータモデルです。
 * PHP5.3以上での動作のみ保証しています。
 * 動作自体はPHP5.2以上から動作します。
 *
 * @category  Plugins
 * @package   Shop
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @version   1.0.0
 */
class Shop_RepeaterOrderDetailModel extends DatabaseModel{
	function __construct($values = array()){
		$loader = new PluginLoader("Shop");
		parent::__construct($loader->loadTable("RepeaterOrderDetailsTable"), $values);
	}

	public function reconstruct(){
		// データを再構築する。
		$connection = DBFactory::getConnection("shop");
		$connection->beginTransaction();
		$prepare = $connection->prepare("TRUNCATE `shop_repeater_order_details`");
		$prepare->execute();
		$sql = "INSERT INTO `shop_repeater_order_details` SELECT `shop_orders`.`order_id` AS `order_id`,`shop_orders`.`order_code` AS `order_code`,`shop_orders`.`customer_id` AS `customer_id`,";
		$sql .= "`shop_orders`.`order_time` AS `order_time`,`shop_orders`.`order_sei` AS `order_sei`,`shop_orders`.`order_mei` AS `order_mei`,`shop_orders`.`order_sei_kana` AS `order_sei_kana`,";
		$sql .= "`shop_orders`.`order_mei_kana` AS `order_mei_kana`,`shop_orders`.`order_email` AS `order_email`,`shop_orders`.`order_zip1` AS `order_zip1`,`shop_orders`.`order_zip2` AS `order_zip2`,";
		$sql .= "`shop_orders`.`order_pref` AS `order_pref`,`shop_orders`.`order_address1` AS `order_address1`,`shop_orders`.`order_address2` AS `order_address2`,`shop_orders`.`order_tel1` AS `order_tel1`,";
		$sql .= "`shop_orders`.`order_tel2` AS `order_tel2`,`shop_orders`.`order_tel3` AS `order_tel3`,`shop_order_packages`.`deliv_sei` AS `deliv_sei`,`shop_order_packages`.`deliv_mei` AS `deliv_mei`,";
		$sql .= "`shop_order_packages`.`deliv_sei_kana` AS `deliv_sei_kana`,`shop_order_packages`.`deliv_mei_kana` AS `deliv_mei_kana`,`shop_order_packages`.`deliv_zip1` AS `deliv_zip1`,";
		$sql .= "`shop_order_packages`.`deliv_zip2` AS `deliv_zip2`,`shop_order_packages`.`deliv_pref` AS `deliv_pref`,`shop_order_packages`.`deliv_address1` AS `deliv_address1`,";
		$sql .= "`shop_order_packages`.`deliv_address2` AS `deliv_address2`,`shop_order_packages`.`deliv_tel1` AS `deliv_tel1`,`shop_order_packages`.`deliv_tel2` AS `deliv_tel2`,";
		$sql .= "`shop_order_packages`.`deliv_tel3` AS `deliv_tel3`,`shop_order_packages`.`delivery_id` AS `delivery_id`,`shop_order_packages`.`delivery_date` AS `delivery_date`,";
		$sql .= "`shop_order_packages`.`delivery_time` AS `delivery_time`,`shop_order_packages`.`deliv_fee` AS `deliv_fee`,`shop_order_packages`.`ship_status` AS `ship_status`,";
		$sql .= "`shop_order_details`.`order_detail_id` AS `order_detail_id`,`shop_order_details`.`order_package_id` AS `order_package_id`,`shop_order_details`.`product_id` AS `product_id`,";
		$sql .= "`shop_order_details`.`product_code` AS `product_code`,`shop_order_details`.`parent_name` AS `parent_name`,`shop_order_details`.`product_name` AS `product_name`,";
		$sql .= "`shop_order_details`.`option1_id` AS `option1_id`,`shop_order_details`.`option1_code` AS `option1_code`,`shop_order_details`.`option1_name` AS `option1_name`,";
		$sql .= "`shop_order_details`.`option2_id` AS `option2_id`,`shop_order_details`.`option2_code` AS `option2_code`,`shop_order_details`.`option2_name` AS `option2_name`,";
		$sql .= "`shop_order_details`.`option3_id` AS `option3_id`,`shop_order_details`.`option3_code` AS `option3_code`,`shop_order_details`.`option3_name` AS `option3_name`,";
		$sql .= "`shop_order_details`.`option4_id` AS `option4_id`,`shop_order_details`.`option4_code` AS `option4_code`,`shop_order_details`.`option4_name` AS `option4_name`,";
		$sql .= "`shop_order_details`.`price` AS `price`,`shop_order_details`.`tax` AS `tax`,`shop_order_details`.`quantity` AS `quantity`,`shop_order_details`.`point_rate` AS `point_rate`,";
		$sql .= "`shop_order_details`.`cancel_flg` AS `cancel_flg`,`shop_order_details`.`cancel_text` AS `cancel_text`,`shop_order_details`.`create_time` AS `create_time`,";
		$sql .= "`shop_order_details`.`update_time` AS `update_time`, count(`counter`.`order_id`) AS `order_repeat` ";
		$sql .= " FROM `shop_order_details` INNER JOIN `shop_order_packages` ON `shop_order_details`.`order_package_id` = `shop_order_packages`.`order_package_id`";
		$sql .= " INNER JOIN `shop_orders` ON `shop_order_packages`.`order_id` = `shop_orders`.`order_id` ";
		$sql .= " LEFT JOIN `shop_orders` AS `counter` ON `shop_orders`.`order_email` = `counter`.`order_email` and `shop_orders`.`order_time` > `counter`.`order_time`";
		$sql .= " GROUP BY `shop_order_details`.`order_detail_id` ORDER BY COUNT(`counter`.`order_id`)";
		$prepare = $connection->prepare($sql);
		$prepare->execute();
		$connection->commit();
	}
		
	function findByPrimaryKey($order_package_id, $product_id, $option1_id = null, $option2_id = null, $option3_id = null, $option4_id = null){
		$this->findBy(array("order_package_id" => $order_package_id, "product_id" => $product_id, "option1_id" => $option1_id, "option2_id" => $option2_id, "option3_id" => $option3_id, "option4_id" => $option4_id));
	}
	
	function findAllByOrderPackage($order_package_id){
		return $this->findAllBy(array("order_package_id" => $order_package_id));
	}
}
?>