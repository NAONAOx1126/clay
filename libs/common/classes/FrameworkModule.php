<?php
/**
 * このシステムにおける全てのモジュールの基底クラスになります。
 * 必ず拡張する必要があり、executeメソッドを実装する必要があります。
 *
 * @category  Common
 * @package   Common
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

/**
 * このシステムにおける全てのモジュールの基底クラスになります。
 * 必ず拡張する必要があり、executeメソッドを実装する必要があります。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
abstract class FrameworkModule{
    /**
	 * デフォルト実行のメソッドになります。
	 * このメソッド以外がモジュールとして呼ばれることはありません。
     *
     * @param array $params モジュールの受け取るパラメータ
     * @access public
     */
	abstract function execute($params);
}
?>
