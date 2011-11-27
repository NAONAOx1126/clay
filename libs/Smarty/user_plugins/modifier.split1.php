<?php
/**
 * Smarty {split1} modifier plugin
 *
 * Type:     modifier<br>
 * Name:     split1<br>
 * Purpose:  split multibyte text to 1 character.<br>
 * @author   Naohisa Minagawa <minagawa at web-life dot co dot jp>
 * @param array $params parameters
 * @param object $smarty Smarty object
 * @param object $template template object
 * @return string|null
 */
function smarty_modifier_split1($str, $delim = "\r\n"){
	// 文字を１文字ずつ分解する。
	$text = array();
	for($i = 0; $i < mb_strlen($str, "UTF-8"); $i ++){
		$text[] = mb_substr($str, $i, 1, "UTF-8");
	}
	$result = implode($delim, $text);
	return $result;
}
?>