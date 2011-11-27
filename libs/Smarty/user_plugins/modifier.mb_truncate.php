<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage PluginsModifier
 */


/**
 * Smarty mb_truncate modifier plugin
 *
 * Type:     modifier<br>
 * Name:     mb_truncate<br>
 * Date:     Feb 24, 2003
 * Purpose:  truncate multibyte text module
 * Input:    string to catenate
 * Example:  {$var|cat:"foo"}
 * @link http://smarty.php.net/manual/en/language.modifier.mb_truncate.php cat
 *          (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @version 1.0
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_mb_truncate($string, $length = 80, $etc = '...') {
    if ($length == 0) {return '';}

    if (mb_strlen($string) > $length) {
        return mb_substr($string, 0, $length).$etc;
    } else {
        return $string;
    }
}
?>
