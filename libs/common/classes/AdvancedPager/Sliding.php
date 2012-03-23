<?php
/**
 * PEAR::Pagerを拡張したページング用のクラスです。
 *
 * @category  Common
 * @package   AdvancedPager
 * @author    Naohisa Minagawa <info@sweetberry.jp>
 * @copyright 2010-2012 Naohisa Minagawa
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache License, Version 2.0
 * @since PHP 5.3
 * @version   1.0.0
 */

/**
 * このクラスの基底クラスとして使用しているPEAR::Pager_Slidingが必要です。
 */
require 'Pager/Sliding.php';

/**
 * スライド形式のPEAR::Pager拡張クラスです。
 * モバイル用のアクセスキーを次のページと前のページのリンクに当てるようになっています。
 *
 * @package AdvancedPager
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class AdvancedPager_Sliding extends Pager_Sliding
{
	/**
	 * 「前のページへ」のリンクに割り当てるアクセスキー用の属性です。
	 *
	 * @access private
	 */
	var $_prevAccessKey;
	
	/**
	 * 「次のページへ」のリンクに割り当てるアクセスキー用の属性です。
	 *
	 * @access private
	 */
	var $_nextAccessKey;

    /**
     * コンストラクタです。
	 * 基本的に使い方はPEAR::Pagerと同じように使えます。
     *
     * @param array $options Pagerの動作を指定する連想配列
     * @access public
     */
    function __construct($options = array())
    {
        //set default Pager_Sliding options
        $this->_delta                 = 2;
        $this->_prevImg               = '&laquo;';
        $this->_nextImg               = '&raquo;';
        $this->_separator             = '|';
        $this->_spacesBeforeSeparator = 3;
        $this->_spacesAfterSeparator  = 3;
        $this->_curPageSpanPre        = '<b>';
        $this->_curPageSpanPost       = '</b>';

		$_allowed_options[] = "prevAccessKey";
		$_allowed_options[] = "nextAccessKey";

		//set custom options
        $err = $this->setOptions($options);
        if ($err !== PAGER_OK) {
            return $this->raiseError($this->errorMessage($err), $err);
        }
        $this->build();
    }

    /**
     * ページ遷移のリンクを構築するためのレンダリング用内部メソッドです。
     *
     * @param string $altText  ページリンクのtitle属性に設定されるテキスト
     * @param string $linkText ページリンクのinnerTextとして設定されるテキスト
     *
     * @return string ページリンクのテキスト
     * @access private
     */
	function _renderLink($altText, $linkText, $accessKey = "")
    {
		$this->_url = $_SERVER["TEMPLATE_NAME"];
        if ($this->_httpMethod == 'GET') {
            if ($this->_append) {
                $href = '?' . $this->_http_build_query_wrapper($this->_linkData);
            } else {
                $href = str_replace('%d', $this->_linkData[$this->_urlVar], $this->_fileName);
            }
            $onclick = '';
            if (array_key_exists($this->_urlVar, $this->_linkData)) {
                $onclick = str_replace('%d', $this->_linkData[$this->_urlVar], $this->_onclick);
            }
            return sprintf('<a href="%s"%s%s%s%s title="%s">%s</a>',
                           htmlentities($this->_url . $href, ENT_COMPAT, 'UTF-8'),
                           empty($this->_classString) ? '' : ' '.$this->_classString,
                           empty($this->_attributes)  ? '' : ' '.$this->_attributes,
                           empty($accessKey)		  ? '' : ' accesskey="'.$accessKey.'"',
                           empty($onclick)            ? '' : ' onclick="'.$onclick.'"',
                           $altText,
                           $linkText
            );
        } elseif ($this->_httpMethod == 'POST') {
            $href = $this->_url;
            if (!empty($_GET)) {
                $href .= '?' . $this->_http_build_query_wrapper($_GET);
            }
            return sprintf("<a href='javascript:void(0)' onclick='%s'%s%s%s title='%s'>%s</a>",
                           $this->_generateFormOnClick($href, $this->_linkData),
                           empty($this->_classString) ? '' : ' '.$this->_classString,
                           empty($this->_attributes)  ? '' : ' '.$this->_attributes,
                           empty($accessKey)		  ? '' : ' accesskey=\''.$accessKey.'\'',
                           $altText,
                           $linkText
            );
        }
        return '';
    }

    /**
     * 「前のページへ」のリンク生成用の内部メソッドです。
     *
     * @param string $url  「前のページへ」で呼ばれるリンク先のURL
     * @param string $link 「前のページへ」のリンクとして表示されるHTML
     *
     * @return string 「前のページへ」のリンク
     * @access private
     */
    function _getBackLink($url='', $link='')
    {
        //legacy settings... the preferred way to set an option
        //now is passing it to the factory
        if (!empty($url)) {
            $this->_path = $url;
        }
        if (!empty($link)) {
            $this->_prevImg = $link;
        }
        $back = '';
        if ($this->_currentPage > 1) {
            $this->_linkData[$this->_urlVar] = $this->getPreviousPageID();
            $back = $this->_renderLink($this->_altPrev, $this->_prevImg, $this->_prevAccessKey)
                  . $this->_spacesBefore . $this->_spacesAfter;
        } else if ($this->_prevImgEmpty !== null && $this->_totalPages > 1) {
            $back = $this->_prevImgEmpty
                  . $this->_spacesBefore . $this->_spacesAfter;
        }
        return $back;
    }

    /**
     * 「次のページへ」のリンク生成用の内部メソッドです。
     *
     * @param string $url  「次のページへ」で呼ばれるリンク先のURL
     * @param string $link 「次のページへ」のリンクとして表示されるHTML
     *
     * @return string 「次のページへ」のリンク
     * @access private
     */
    function _getNextLink($url='', $link='')
    {
        //legacy settings... the preferred way to set an option
        //now is passing it to the factory
        if (!empty($url)) {
            $this->_path = $url;
        }
        if (!empty($link)) {
            $this->_nextImg = $link;
        }
        $next = '';
        if ($this->_currentPage < $this->_totalPages) {
            $this->_linkData[$this->_urlVar] = $this->getNextPageID();
            $next = $this->_spacesAfter
                  . $this->_renderLink($this->_altNext, $this->_nextImg, $this->_nextAccessKey)
                  . $this->_spacesBefore . $this->_spacesAfter;
        } else if ($this->_nextImgEmpty !== null && $this->_totalPages > 1) {
            $next = $this->_spacesAfter
                  . $this->_nextImgEmpty
                  . $this->_spacesBefore . $this->_spacesAfter;
        }
        return $next;
    }
}
?>