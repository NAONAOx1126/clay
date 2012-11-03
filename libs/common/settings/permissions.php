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

// ホームに書き込み権限が必要です。
if(!is_writable(CLAY_ROOT)){
	echo "\"".CLAY_ROOT."\"に書き込み許可を与えてください。";
	exit;
}
// configureに書き込み権限が必要です。
if(!is_writable(FRAMEWORK_CONFIGURE_HOME)){
	echo "\"".FRAMEWORK_CONFIGURE_HOME."\"に書き込み許可を与えてください。";
	exit;
}
// cacheに書き込み権限が必要です。
if(!is_writable(FRAMEWORK_CACHE_HOME)){
	echo "\"".FRAMEWORK_CACHE_HOME."\"に書き込み許可を与えてください。";
	exit;
}
// contentsに書き込み権限が必要です。
if(!is_writable(FRAMEWORK_CONTENTS_HOME)){
	echo "\"".FRAMEWORK_CONTENTS_HOME."\"に書き込み許可を与えてください。";
	exit;
}
// logsに書き込み権限が必要です。
if(!is_writable(FRAMEWORK_LOGS_HOME)){
	echo "\"".FRAMEWORK_LOGS_HOME."\"に書き込み許可を与えてください。";
	exit;
}
?>
