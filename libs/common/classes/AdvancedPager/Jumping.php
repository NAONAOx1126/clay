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

/**
 * This class based on PEAR::Pager_Jumping class.
 */
require 'Pager/Jumping.php';

/**
 * Extended for PEAR::Pager to generate jump pager.
 * This class append mobile key action to prev and next page.
 */
class AdvancedPager_Jumping extends Pager_Jumping
{
	/**
	 * Access key for prev page.
	 */
	var $_prevAccessKey;
	
	/**
	 * Access key for next page
	 */
	var $_nextAccessKey;

    /**
     * Create pager object.
	 * Same as PEAR::Pager similery
     *
     * @param array $options Pager options
     */
    function __construct($options = array())
    {
		$_allowed_options[] = "prevAccessKey";
		$_allowed_options[] = "nextAccessKey";
        $err = $this->setOptions($options);
        if ($err !== PAGER_OK) {
            return $this->raiseError($this->errorMessage($err), $err);
        }
        $this->build();
    }

    /**
     * Internal method for generate page links.
     *
     * @param string $altText  "title" text for page link.
     * @param string $linkText "innerText" text for page link.
     * @return string pagelink html content.
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
     * Generate link for prev page.
     *
     * @param string $url  link url for prev page.
     * @param string $link internal content for prev page.
     * @return string link content for prev page.
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
     * Generate link for next page.
     *
     * @param string $url  link url for next page.
     * @param string $link internal content for next page
     * @return string link content for next page.
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