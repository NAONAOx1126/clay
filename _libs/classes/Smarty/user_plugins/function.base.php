<?php
/**
 * This file is part of CLAY Framework for view-module based system.
 *
 * @author    Naohisa Minagawa <info@clay-system.jp>
 * @copyright Copyright (c) 2010, Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   3.0.0
 */
function smarty_function_base($params, $smarty, $template){
	if(substr(CLAY_SUBDIR, -1) == "/"){
		return substr(CLAY_SUBDIR, 0, -1);
	}
	return CLAY_SUBDIR;
}
?>