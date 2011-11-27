<?php
class Base_Pages_PageBack extends FrameworkModule{
	function execute($params){
		// 戻る処理の時にはメッセージなしのエラー例外を発行する。
		// 戻り先の画面はshiftパラメータに設定する。
		if(!empty($_POST["back"])){
			unset($_POST["back"]);
			throw new ShiftException();
		}
	}
}
?>
