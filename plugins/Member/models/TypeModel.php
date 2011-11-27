<?php
// この処理で使用するテーブルモデルをインクルード
LoadTable("CustomerTypesTable", "Members");
LoadTable("TypesTable", "Members");

/**
 * 顧客種別情報のモデルクラス
 */
class TypeModel extends DatabaseModel{
	function __construct($values = array()){
		parent::__construct(new TypesTable(), $values);
	}
	
	function findByPrimaryKey($type_id){
		$this->findBy(array("type_id" => $type_id));
	}
	
	function getSelected($customer_id){
		$types = new TypesTable();
		$customerTypes = new CustomerTypesTable();
		$select = new DatabaseSelect($types);
		$select->addColumn($types->_W);
		$select->joinLeft($customerTypes, array($customerTypes->type_id." = ".$types->type_id));
		$select->addWhere($customerTypes->customer_id." IS NOT NULL");
		$result = $select->execute();
		$types = array();
		if(is_array($result)){
			foreach($result as $data){
				$types[$data["type_id"]] = new TypeModel($data);
			}
		}
		return $types;
	}
	
	function getSelectable($customer_id){
		$types = new TypesTable();
		$customerTypes = new CustomerTypesTable();
		$select = new DatabaseSelect($types);
		$select->addColumn($types->_W);
		$select->joinLeft($customerTypes, array($customerTypes->type_id." = ".$types->type_id, $customerTypes->customer_id." = ?"), array($customer_id));
		$select->addWhere($customerTypes->customer_id." IS NULL");
		$result = $select->execute();
		$types = array();
		if(is_array($result)){
			foreach($result as $data){
				$types[$data["type_id"]] = new TypeModel($data);
			}
		}
		return $types;
	}
	
	function getAll(){
		$types = new TypesTable();
		$select = new DatabaseSelect($types);
		$select->addColumn($types->_W);
		$result = $select->execute();
		$types = array();
		if(is_array($result)){
			foreach($result as $data){
				$types[$data["type_id"]] = new TypeModel($data);
			}
		}
		return $types;
	}
}
?>