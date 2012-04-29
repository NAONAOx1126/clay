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
 require("Pager.php");

/**
 * Extended for PEAR::Pager to generate pager.
 * This class append mobile key action to prev and next page.
 */
class AdvancedPager extends Pager
{
    /**
     * コンストラクタです。
	 * 渡すことのできるパラメータは基本的にPEAR::Pagerと同じです。
     *
     * -------------------------------------------------------------------------
	 *
     * 有効なパラメータ:
     *  - mode       (string): "Jumping"か"Sliding"を指定します。
     *                         ページ遷移リンクの表示形式が変わります。
     *  - totalItems (int):    表示結果全体の件数（全ページ分）
     *  - perPage    (int):    １ページに表示する最大件数
     *  - delta      (int):    現在のページの前後に表示するページ数
     *  - linkClass  (string): ページリンクに設定するCSS用のクラス名
     *  - append     (bool):   trueならページのIDをGETパラメータに付加します。
     *                         falseなら付加しません。
     *  - httpMethod (string): ページリンクのメソッド（"GET"か"POST"）を指定します。
     *  - importQuery (bool):  ページリンクにページ表示の際に渡されたパラメータを引き継ぐかどうか指定します。
     *  - path       (string): ページリンクの絶対パスです。
     *  - fileName   (string): ページリンクのファイル名です。
     *  - urlVar     (string): ページリンクのページ番号のパラメータ名です。
     *  - altPrev    (string): 「前のページへ」のリンクのタイトル属性です。
     *  - altNext    (string): 「次のページへ」のリンクのタイトル属性です。
     *  - altPage    (string): "ページ番号"リンクのタイトル属性です。
     *  - prevImg    (string): 「前のページへ」のリンクに使用するHTMLです。
     *  - nextImg    (string): 「次のページへ」のリンクに使用するHTMLです。
     *  - separator  (string): "ページ番号"リンクの各番号を区切るテキストを指定します。
     *  - spacesBeforeSeparator (int):    区切り文字の前に設定する空白の長さを指定します。
     *  - firstPagePre (string): 「最初のページへ」のリンクの前に表示するテキストを指定します。
     *  - firstPageText (string): 「最初のページへ」のリンクのテキストを設定します。
     *  - firstPagePost (string): 「最初のページへ」のリンクの後に表示するテキストを指定します。
     *  - lastPagePre (string): 「最後のページへ」のリンクの前に表示するテキストを指定します。
     *  - lastPageText (string): 「最後のページへ」のリンクのテキストを設定します。
     *  - lastPagePost (string): 「最後のページへ」のリンクの後に表示するテキストを指定します。
     *  - spacesAfterSeparator(int):  区切り文字の後に設定する空白の長さを指定します。
     *  - firstLinkTitle (string):  「LINK-rel=first」タグに使用するタイトル
     *  - lastLinkTitle (string): 「LINK-rel=last」タグに使用するタイトル
     *  - prevLinkTitle (string):「LINK-rel=prev」タグに使用するタイトル
     *  - nextLinkTitle (string):「LINK-rel=next」タグに使用するタイトル
     *  - curPageLinkClassName (string): 現在のページリンクに使用するクラス名
     *  - clearIfVoid(bool):   リストが２ページ以上にならない場合ページリンクを非表示にする場合はtrue
     *  - extraVars (array):   追加で指定するURLパラメータ
     *  - excludeVars (array): 除外するURLパラメータ
     *  - itemData   (array):  一覧表示する実データ
     *  - useSessions (bool):  ページ情報をセッションに保持する場合はtrue
     *  - closeSession (bool): ページ遷移時にセッションをクリアする場合にはtrue
     *  - sessionVar (string): ページ情報を保持するセッションのキー名
     *  - pearErrorMode (constant): エラー時の処理方法を指定する。デフォルトではPEAR::Errorを返す。
	 *  - prevAccessKey(string):  「前のページへ」に使用するアクセスキー
	 *  - nextAccessKey(string):  「次のページへ」に使用するアクセスキー
	 *
     * -------------------------------------------------------------------------
	 *
     * 必須となるパラメータは：
     *  - append=falseの場合、fileName（デフォルトではappend=true）
     *  - itemDataもしくはtotalItems (itemDataが渡された場合、totalItemsは上書きされます。)
	 *
     * -------------------------------------------------------------------------
     *
     * @param mixed $options Pagerの動作を指定する連想配列
     *
     * @access public
     */
    function __construct($options = array())
    {
        //this check evaluates to true on 5.0.0RC-dev,
        //so i'm using another one, for now...
        //if (version_compare(phpversion(), '5.0.0') == -1) {
        if (get_class($this) == 'pager') { //php4 lowers class names
            // assign factoried method to this for PHP 4
            eval('$this = AdvancedPager::factory($options);');
        } else { //php5 is case sensitive
            $msg = 'Pager constructor is deprecated.'
                  .' You must use the "Pager::factory($params)" method'
                  .' instead of "new Pager($params)"';
            trigger_error($msg, E_USER_ERROR);
        }
    }

    /**
     * Pagerの実際のインスタンスを指定された設定を元に生成するファクトリメソッドです。
     *
     * @param array $options Pagerの動作を指定する連想配列
     *
     * @return object Pager
     * @static
     * @access public
     */
    function &factory($options = array())
    {
        $mode = (isset($options['mode']) ? ucfirst($options['mode']) : 'Jumping');
        $classname = 'AdvancedPager_' . $mode;
        $classfile = 'AdvancedPager' . DIRECTORY_SEPARATOR . $mode . '.php';

        // Attempt to include a custom version of the named class, but don't treat
        // a failure as fatal.  The caller may have already included their own
        // version of the named class.
        if (!class_exists($classname)) {
            include_once $classfile;
        }

        // If the class exists, return a new instance of it.
        if (class_exists($classname)) {
            $pager = new $classname($options);
            return $pager;
        }

        $null = null;
        return $null;
    }

    // }}}
}
?>