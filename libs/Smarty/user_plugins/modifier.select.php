<?php
/**
 * Smarty {select} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     select<br>
 * Purpose:  modify value prefer input<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_modifier_select($value, $key, $target, $method = ""){
	if(isset($_POST[$key])){
		$value = $_POST[$key];
	}
	if($method != "" && method_exists($value, $method)){
		if($value->$method($target)){
			return " selected";
		}
		return "";
	}
	if(!is_array($value)){
		if($value == $target){
			return " selected";
		}
	}else{
		if(in_array($target, $value)){
			return " selected";
		}
	}
	return "";
}
?>