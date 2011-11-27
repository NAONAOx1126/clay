<?php
class Base_Pages_ClearInput extends FrameworkModule{
	function execute($params){
		$_SESSION["INPUT_DATA"][TEMPLATE_DIRECTORY] = array();
		$_POST = array();
	}
}
?>
