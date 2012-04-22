<?php
/**
 * Smarty {input} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     input<br>
 * Purpose:  modify value prefer input<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_modifier_input($value, $key, $subkey = ""){
	if(isset($_POST[$key])){
		if($subkey != "" && is_array($_POST[$key])){
			return $_POST[$key][$subkey];
		}else{
			return $_POST[$key];
		}
	}
	return $value;
}
?>