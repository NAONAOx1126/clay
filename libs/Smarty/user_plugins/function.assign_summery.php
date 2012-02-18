<?php
/**
 * Smarty {assign_summery} function plugin
 *
 * Type:     function<br>
 * Name:     assign_summery<br>
 * Purpose:  assign summery array from array or obect - array<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_function_assign_summery($params, $smarty, $template)
{
    // varパラメータは必須です。
    if (empty($params['var'])) {
        trigger_error("assign_summery: missing var parameter", E_USER_WARNING);
        return;
    }
    // valueパラメータは必須です。
    if (empty($params['value'])) {
        trigger_error("assign_summery: missing value parameter", E_USER_WARNING);
        return;
    }
    // valueパラメータは必須です。
    if (empty($params['title'])) {
        trigger_error("assign_summery: missing title parameter", E_USER_WARNING);
        return;
    }
    // valueパラメータは必須です。
    if (empty($params['key'])) {
        trigger_error("assign_summery: missing key parameter", E_USER_WARNING);
        return;
    }
    
	$title = $params['title'];
	$key = $params['key'];
    $summery = array();
    foreach($params['value'] as $data){
    	if(is_array($data)){
    		if(!isset($summery[$data[$title]][$key])){
    			$summery[$data[$title]][$title] = $data[$title];
	    		$summery[$data[$title]][$key] = 0;
    		}
    		$summery[$data[$title]][$key] += $data[$key];
    	}else{
    		if(!isset($summery[$data->$title][$key])){
    			$summery[$data->$title][$title] = $data->$title;
	    		$summery[$data->$title][$key] = 0;
    		}
    		$summery[$data->$title][$key] += $data->$key;
    	}
    }
    $result = array();
    foreach($summery as $data){
    	$result[] = $data;
    }
    $template->assign($params['var'], $result);
}
?>