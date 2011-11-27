<?php
// この処理で使用するテーブルモデルをインクルード
$memberPluginLoader = new PluginLoader();
$memberPluginLoader->LoadTable("PrefsTable");
$memberPluginLoader = new PluginLoader("Member");
$memberPluginLoader->LoadTable("CustomersTable");
$memberPluginLoader->LoadTable("CustomerOptionsTable");
$memberPluginLoader->LoadTable("CustomerOptionModel");

/**
 * 顧客情報のモデルクラス
 */
class CustomerModel extends DatabaseModel{
	var $types;

	var $options;

	function __construct($values = array()){
		$this->types = null;
		$this->options = null;
		parent::__construct(new CustomersTable(), $values);
	}
	
	/**
	 * 顧客区分のリストを取得する。
	*/
	function types(){
		if($this->types == null){
			$types = new CustomerTypeModel();
			$this->types = $types->findAllByCustomer($this->customer_id);
		}
		
		return $this->types;
	}
	
	/**
	 * 顧客オプションのリストを取得する。
	*/
	function option($name){
		if($this->options == null){
			$options = new CustomerOptionModel();
			$result = $options->findAllByCustomer($this->customer_id);
			if(is_array($result)){
				foreach($result as $data){
					$this->options[$data->option_name] = new CustomerOptionModel($data);
				}
			}
		}
		
		return $this->options[$name];
	}
	
	/**
	 * 都道府県の名前を取得
	 */
	 function pref_name(){
		$prefs = new PrefsTable();
		$select = new DatabaseSelect($prefs);
		$select->addColumn($prefs->name)->addWhere($prefs->id." = ?", array($this->pref));
		$result = $select->execute();
		return $result[0]["name"];
	 }
	
	function prev($condition = ""){
		$prefs = new PrefsTable();
		$customerOptions = new CustomerOptionsTable();
		
		$select = new DatabaseSelect($customerOptions);
		$select->addColumn($customerOptions->option_name)->addGroupBy($customerOptions->option_name);
		$options = $select->execute();
		
		$select = new DatabaseSelect($this->access);
		$select->addColumn($this->access->_W);
		$select->addColumn("CASE WHEN ".$this->access->sex." = 1 THEN '男性' WHEN ".$this->access->sex." = 2 THEN '女性' ELSE '' END", "sex_name");
		$select->addColumn($prefs->name, "pref_name")->joinLeft($prefs, array($this->access->pref." = ".$prefs->id));
		foreach($options as $option){
			$optionKey = $option["option_name"];
			$customerOptions = new CustomerOptionsTable();
			$customerOptions->setAlias("opt_".$optionKey);
			$select->addColumn($customerOptions->option_value, $optionKey);
			$select->joinLeft($customerOptions, array($this->access->customer_id." = ".$customerOptions->customer_id, $customerOptions->option_name." = '".$optionKey."'"));
		}
		if(!empty($condition)){
			$select->addWhere($condition);
		}
		$select->addWhere($this->access->create_time." < ?", array($this->create_time));
		$select->addOrder($this->access->create_time, true);
		$result = $select->execute(0, 1);
		return $result;
	}
		
	function next($condition = ""){
		$prefs = new PrefsTable();
		$customerOptions = new CustomerOptionsTable();
		
		$select = new DatabaseSelect($customerOptions);
		$select->addColumn($customerOptions->option_name)->addGroupBy($customerOptions->option_name);
		$options = $select->execute();
		
		$select = new DatabaseSelect($this->access);
		$select->addColumn($this->access->_W);
		$select->addColumn("CASE WHEN ".$this->access->sex." = 1 THEN '男性' WHEN ".$this->access->sex." = 2 THEN '女性' ELSE '' END", "sex_name");
		$select->addColumn($prefs->name, "pref_name")->joinLeft($prefs, array($this->access->pref." = ".$prefs->id));
		foreach($options as $option){
			$optionKey = $option["option_name"];
			$customerOptions = new CustomerOptionsTable();
			$customerOptions->setAlias("opt_".$optionKey);
			$select->addColumn($customerOptions->option_value, $optionKey);
			$select->joinLeft($customerOptions, array($this->access->customer_id." = ".$customerOptions->customer_id, $customerOptions->option_name." = '".$optionKey."'"));
		}
		if(!empty($condition)){
			$select->addWhere($condition);
		}
		$select->addWhere($this->access->create_time." > ?", array($this->create_time));
		$select->addOrder($this->access->create_time);
		$result = $select->execute(0, 1);
		return $result;
	}
		
	function findByPrimaryKey($customer_id){
		$this->findBy(array("customer_id" => $customer_id));
	}
	
	function findByCode($customer_code){
		$this->findBy(array("customer_code" => $customer_code));
	}
	
	function findByMobileId($mobile_id){
		$this->findBy(array("mobile_id" => $mobile_id));
	}
	
	function findByExternalId($external_id){
		$this->findBy(array("external_id" => $external_id));
	}
	
	function findByEmail($email){
		$this->findBy(array("email" => $email));
	}		
	
	function findByEmailMobile($email){
		$this->findBy(array("email_mobile" => $email));
	}
	
	function getNewCustomersArray($condition, $pager = array()){
		$prefs = new PrefsTable();
		$customerOptions = new CustomerOptionsTable();
		
		$select = new DatabaseSelect($customerOptions);
		$select->addColumn($customerOptions->option_name)->addGroupBy($customerOptions->option_name);
		$options = $select->execute();
		
		$select = new DatabaseSelect($this->access);
		$select->addColumn($this->access->_W);
		$select->addColumn("CASE WHEN ".$this->access->sex." = 1 THEN '男性' WHEN ".$this->access->sex." = 2 THEN '女性' ELSE '' END", "sex_name");
		$select->addColumn($prefs->name, "pref_name")->joinLeft($prefs, array($this->access->pref." = ".$prefs->id));
		foreach($options as $option){
			$optionKey = $option["option_name"];
			$customerOptions = new CustomerOptionsTable();
			$customerOptions->setAlias("opt_".$optionKey);
			$select->addColumn($customerOptions->option_value, $optionKey);
			$select->joinLeft($customerOptions, array($this->access->customer_id." = ".$customerOptions->customer_id, $customerOptions->option_name." = '".$optionKey."'"));
			if(!empty($condition[$optionKey])){
				$select->addWhere($customerOptions->option_value." LIKE ?", array("%".$condition[$optionKey]."%"));
				unset($condition[$optionKey]);
			}
		}
		
		foreach($condition as $key => $value){
			if(isset($this->access->$key) && !empty($value)){
				$select->addWhere($this->access->$key." LIKE ?", array("%".$value."%"));
			}
		}
		
		$select->addOrder($this->access->create_time, true);

		if(!empty($pager)){
			$result = $select->executePager($pager);
		}else{
			$result = $select->execute();
		}
		
		return $result;
	}
	
		
	function getCustomersArray($condition, $pager = array()){
		$prefs = new PrefsTable();
		$customerOptions = new CustomerOptionsTable();
		
		$select = new DatabaseSelect($customerOptions);
		$select->addColumn($customerOptions->option_name)->addGroupBy($customerOptions->option_name);
		$options = $select->execute();
		
		$select = new DatabaseSelect($this->access);
		$select->addColumn($this->access->_W);
		$select->addColumn("CASE WHEN ".$this->access->sex." = 1 THEN '男性' WHEN ".$this->access->sex." = 2 THEN '女性' ELSE '' END", "sex_name");
		$select->addColumn($prefs->name, "pref_name")->joinLeft($prefs, array($this->access->pref." = ".$prefs->id));
		foreach($options as $option){
			$optionKey = $option["option_name"];
			$customerOptions = new CustomerOptionsTable();
			$customerOptions->setAlias("opt_".$optionKey);
			$select->addColumn($customerOptions->option_value, $optionKey);
			$select->joinLeft($customerOptions, array($this->access->customer_id." = ".$customerOptions->customer_id, $customerOptions->option_name." = '".$optionKey."'"));
			if(!empty($condition[$optionKey])){
				$select->addWhere($customerOptions->option_value." = ?", array($condition[$optionKey]));
				unset($condition[$optionKey]);
			}
			if(!empty($condition[">:".$optionKey])){
				$select->addWhere($customerOptions->option_value." > ?", array($condition[">:".$optionKey]));
				unset($condition[">:".$optionKey]);
			}
			if(!empty($condition["<:".$optionKey])){
				$select->addWhere($customerOptions->option_value." < ?", array($condition["<:".$optionKey]));
				unset($condition["<:".$optionKey]);
			}
			if(!empty($condition[">=:".$optionKey])){
				$select->addWhere($customerOptions->option_value." >= ?", array($condition[">=:".$optionKey]));
				unset($condition[">=:".$optionKey]);
			}
			if(!empty($condition["<=:".$optionKey])){
				$select->addWhere($customerOptions->option_value." <= ?", array($condition["<=:".$optionKey]));
				unset($condition["<=:".$optionKey]);
			}
			if(!empty($condition["!=:".$optionKey])){
				$select->addWhere($customerOptions->option_value." != ?", array($condition["!=:".$optionKey]));
				unset($condition["!=:".$optionKey]);
			}
			if(!empty($condition["like:".$optionKey])){
				$select->addWhere($customerOptions->option_value." LIKE ?", array("%".$condition["like:".$optionKey]."%"));
				unset($condition["like:".$optionKey]);
			}
			if(!empty($condition["in:".$optionKey])){
				$values = explode(",", $condition["in:".$optionKey]);
				$replace = array();
				$vals = array();
				foreach($values as $v){
					$replace[] = "?";
					$vals[] = trim($v);
				}
				$select->addWhere($customerOptions->option_value." IN (".implode(", ", $replace).")", $vals);
				unset($condition["in:".$optionKey]);
			}
			if(!empty($condition["order:".$optionKey])){
				$select->addOrder($customerOptions->option_value, ($condition["order:".$optionKey]?true:false));
				unset($condition["order:".$optionKey]);
			}
			if(!empty($condition["order_num:".$optionKey])){
				$select->addOrder("CAST(".$customerOptions->option_value." AS SIGNED)", ($condition["order_num:".$optionKey]?true:false));
				unset($condition["order_num:".$optionKey]);
			}
		}
		
		if(is_array($condition)){
			foreach($condition as $key => $value){
				if(strpos($key, ":") !== FALSE){
					list($prefix, $key) = explode(":", $key);
					if(isset($this->access->$key) && !empty($value)){
						switch($prefix){
							case ">":
								$select->addWhere($this->access->$key." > ?", array($value));
								break;
							case "<":
								$select->addWhere($this->access->$key." < ?", array($value));
								break;
							case ">=":
								$select->addWhere($this->access->$key." >= ?", array($value));
								break;
							case "<=":
								$select->addWhere($this->access->$key." <= ?", array($value));
								break;
							case "!=":
								$select->addWhere($this->access->$key." != ?", array($value));
								break;
							case "like":
								$select->addWhere($this->access->$key." LIKE ?", array("%".$value."%"));
								break;
							case "in":
								$values = explode(",", $value);
								$replace = array();
								$vals = array();
								foreach($values as $value){
									$replace[] = "?";
									$vals[] = trim($value);
								}
								$select->addWhere($this->access->$key." IN (".implode(", ", $replace).")", $vals);
								break;
							case "order":
								$select->addOrder($this->access->$key, ($value?true:false));
								break;
							case "order_num":
								$select->addOrder("CAST(".$this->access->$key." AS SIGNED)", ($value?true:false));
								break;
						}
					}
				}else{
					if(isset($this->access->$key) && !empty($value)){
						$select->addWhere($this->access->$key." = ?", array($value));
					}
				}
			}
		}
		
		if(!empty($pager)){
			$result = $select->executePager($pager);
		}else{
			$result = $select->execute();
		}
		
		return $result;
	}
	
	function getSummery($condition){
	}
}
?>