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
 * このクラスの基底クラスとして使用しているMBPDFが必要です。
 */
include(FRAMEWORK_FPDF_LIBRARY_HOME."/MBFPDF.php");

/**
 * PDFのドキュメントを作成するためのクラスです。
 * 元のテンプレートにテキストを埋めるという形になります。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class PdfDocument extends MBFPDF{
	/**
	 * 一行の高さを保持する属性です。
	 *
	 * @access private
	 */
	private $lineHeight;

    /**
	 * コンストラクタです。
	 * PDFのドキュメント全体を定義します。
     *
	 * @params string $template ページのテンプレートに使用するファイルパス
	 * @params integer $page	最初のページとして設定するテンプレート元のページ番号
     * @access public
     */
	public function __construct($template, $page = 1){
		$template_path = MINES_HOME.PATH_SEPARATOR.'contents'.PATH_SEPARATOR.$_SERVER["USER_TEMPLATE"].PATH_SEPARATOR.$template;
		parent::FPDF();

		$this->setSourceFile($template_path);
		$this->AddMBFont(KOZMIN, "SJIS");
		$this->AddTemplatePage($page);
		
		$this->lineHeight = 14;
	}
	
    /**
	 * ページに設定するフォントを指定します。
     *
	 * @params string $fontName 使用するフォントの名前
	 * @params string $fontStyle 使用するフォントのスタイル
	 * @params integer $fontSize 使用するフォントのサイズ
     * @access public
     */
	public function SetFont($fontName, $fontStyle = "", $fontSize = 12){
		$this->lineHeight = floor($fontSize + 2);
		parent::SetFont($fontName, $fontStyle, $fontSize);
	}
	
    /**
	 * ページに設定するフォントを指定します。
     *
	 * @params integer $srcPage 最初のページとして設定するテンプレート元のページ番号
     * @access public
     */
	public function AddTemplatePage($srcPage){
		$tplidx = $this->ImportPage($srcPage);
		$this->AddPage();
		$this->useTemplate($tplidx);
	}
	
    /**
	 * ページに設定するフォントを指定します。
     *
	 * @params integer $x テキストを挿入する位置のX座標
	 * @params integer $y テキストを挿入する位置のY座標
	 * @params string $text 挿入するテキスト
     * @access public
     */
	public function SetText($x, $y, $text){
		$this->SetXY($x, $y);
		$this->Write($this->lineHeight, mb_convert_encoding($text, "Shift_JIS", "UTF-8"));
	}
}
?>
