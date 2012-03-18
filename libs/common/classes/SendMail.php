<?php
/**
 * メール送信に使用するクラスです。
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
 * テキスト形式のメール送信に使用するクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class SendMail{
	/**
	 * メールログ保存用のデータベース
	 */
	protected $db;
	
	/**
	 * メールの送信元（名称）
	 */
	protected $from;
	
	/**
	 * メールの送信元アドレス
	 */
	protected $fromAddress;
	
	/**
	 * メールの送信先（名称）
	 */
	protected $to;
	
	/**
	 * メールの送信先アドレス
	 */
	protected $toAddress;
	
	/**
	 * メールのタイトル
	 */
	protected $subject;

	/**
	 * メールの本文
	 */
	protected $body;

    /**
	 * コンストラクタです。テキストメールのための初期設定を行います。
	 *
     * @access public
     */
	public function __construct(){
		$this->db = null;
		$this->body ="";
	}
	
    /**
	 * メールの送信元を設定します。
	 *
	 * @params string $address 送信元のメールアドレス
	 * @params string $name 送信元のメールアドレスに対応する名前
     * @access public
     */
	public function setFrom($address, $name = ""){
		$this->fromAddress = $address;
		if(!empty($name)){
			$this->from = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($name, "JIS", "UTF-8"))."?= <".$address.">";
		}else{
			$this->from = $address;
		}
	}
	
    /**
	 * メールの送信先を設定します。
	 *
	 * @params string $address 送信先のメールアドレス
	 * @params string $name 送信先のメールアドレスに対応する名前
     * @access public
     */
	public function setTo($address, $name = ""){
		$this->toAddress = $address;
		if(!empty($name)){
			$this->to = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($name, "JIS", "UTF-8"))."?= <".$address.">";
		}else{
			$this->to = $address;
		}
	}
	
    /**
	 * メールのタイトルを設定します。
	 *
	 * @params string $subject メールのタイトル
     * @access public
     */
	public function setSubject($subject){
		$this->subject = "=?iso-2022-jp?B?".base64_encode(mb_convert_encoding($subject, "JIS", "UTF-8"))."?=";
	}
	
    /**
	 * メールの本文を設定します。
	 *
	 * @params string $parts メールの本文
     * @access public
     */
	public function addBody($parts){
		$this->body .= $parts;
	}
	
    /**
	 * メールログ書き込み用のデータベースを設定します。
	 *
	 * @params MDB2 $db メールログ書き込み用データベース
     * @access public
     */
	public function setDatabase($db){
		$this->db = $db;
	}
	
    /**
	 * テキストメールを送信します。
	 *
	 * @parmas string $contentType メール全体のコンテンツタイプ。
	 * @params string $suffix メールの最後に付加する文字列メール毎に違う文章を設定する際に利用。
     * @access public
     */
	public function send($contentType = "text/plain", $suffix = ""){
		// メールヘッダを作成
		$this->sendRaw($this->from, $this->fromAddress, $this->to, $this->subject, $contentType."; charset=iso-2022-jp", mb_convert_encoding($this->body, "JIS", "UTF-8"));
		
		try{
			// メールログのテーブルモデルを読み込み
			LoadTable("MaillogsTable");
			$maillogs = new MaillogsTable();
					
			// データベースINSERTモデルの読み込み
			$insert = new DatabaseInsert($maillogs, $this->db);
			
			// 設定するデータ配列を定義
			$values = array();
			$values["mail_from"] = $this->fromAddress;
			$values["mail_to"] = $this->toAddress;
			$values["subject"] = $this->subject;
			$values["body"] = $this->body;
			$values["mail_time"] = date("Y-m-d H:i:s");
			
			// INSERTの実行
			$insert->execute($values);
		}catch(Exception $e){
			// メールログの書き込み失敗はエラーと見なさない。
		}
	}
	
    /**
	 * テキストメールを返信します。
	 * 送信時、送信元と送信先が入れ替わります。
	 *
	 * @parmas string $contentType メール全体のコンテンツタイプ。
	 * @params string $suffix メールの最後に付加する文字列メール毎に違う文章を設定する際に利用。
     * @access public
     */
	public function reply($contentType = "text/plain", $suffix = ""){
		$this->sendRaw($this->to, $this->toAddress, $this->from, $this->subject, $contentType."; charset=iso-2022-jp", mb_convert_encoding($this->body, "JIS", "UTF-8"));
	}
	
	public function sendRaw($from, $fromAddress, $to, $subject, $contentType, $body){
		// メールヘッダを作成
		$header = "";
		$header .= "From: ".$from."\n";
		$header .= "Reply-To: ".$from."\n";
		$header .= "MIME-Version: 1.0\n";
		$header .= "Content-Type: ".$contentType."\n";
		$header .= "Content-Transfer-Encoding: 7bit\n";
		$header .= "X-Mailer: PHP/".phpversion();
		
		mail($to, $subject, $body, $header, "-f ".$fromAddress);		
	}

	// エンコード関数
	function qp_encode($text){	
		if(function_exists('quoted_printable_encode')){
			return quoted_printable_encode($text);
		}elseif(function_exists('imap_8bit')){
			return imap_8bit($text);
		}else{
			$arrEncodeSupport = mb_list_encodings();
			if(array_search('Quoted-Printable', $arrEncodeSupport) != FALSE){
				return mb_convert_encoding($text, 'Quoted-Printable', "JIS");
			}else{
				$crlf="\r\n";
				$text=trim($text);
		
				$lines   = preg_split("/(\r\n|\n|\r)/s", $text);
		
				$out     = '';
				$temp = '';
		
				foreach ($lines as $line){
					for ($j = 0; $j < strlen($line); $j++){
						$char = substr ( $line, $j, 1 );
						$ascii = ord ( $char );
			
						if ( $ascii < 32 || $ascii == 61 || $ascii > 126 ){
							$char = '=' . strtoupper ( dechex( $ascii ) );
						}
			
						if ( ( strlen ( $temp ) + strlen ( $char ) ) >= 76 ){
							$out .= $temp . '=' . $crlf;   $temp = '';
						}
						$temp .= $char;
					}
				}
				$out .= $temp;
			
				return trim ( $out );
			}
		}
	}
}

/**
 * HTML形式のメール送信に使用するクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class SendPCHtmlMail extends SendMail{
	/**
	 * プレフィックス１
	 * これはマルチパートのエンベロープ部分の為に使用します。
	 */
	private $prefix;
	
	/**
	 * HTMLメールを読めないクライアントのための代替テキスト
	 */
	private $extBody;

	/**
	 * マルチパート用区切り文字列
	 * multipart/alternativeに使用します。
	 */
	private $boundary;

    /**
	 * コンストラクタです。HTMLメールのための初期設定を行います。
	 *
     * @access public
     */
	public function __construct(){
		parent::__construct();
		
		$this->extBody = "";
		
		// マルチパートのバウンダリを設定
		$this->boundary = "MINES-" . uniqid("b");

		// Bodyのヘッダ部分を設定
		$this->prefix = "\n--".$this->boundary."\n";
		$this->prefix .= "Content-Type: text/html; charset=\"iso-2022-jp\"\n";
		$this->prefix .= "Content-Transfer-Encoding: quoted-printable\n\n";
	}
	
    /**
	 * HTMLメールが読めないクライアントのための代替テキストを設定します。
	 *
	 * @params string $parts 代替テキストとして追加指定する文字列
     * @access public
     */
	public function addExtBody($parts){
		$this->extBody .= $parts;
	}
	
    /**
	 * HTMLメールを送信します。
	 *
	 * @parmas string $contentType メール全体のコンテンツタイプ。マルチパートで送信するこのクラスでは原則使用しない。
	 * @params string $suffix メールの最後に付加する文字列メール毎に違う文章を設定する際に利用。
     * @access public
     */
	public function send($contentType = "", $suffix = ""){
		// コンテンツタイプを設定
		if(empty($contentType)){
			$contentType = "multipart/alternative;boundary=\"".$this->boundary."\"";
		}
		
		// 代替テキストを設定
		$extBodyHead = "\n--".$this->boundary."\n";
		$extBodyHead .= "Content-Type: text/plain; charset=\"iso-2022-jp\"\n";
		$extBodyHead .= "Content-Transfer-Encoding: 7bit\n\n";
		if(empty($this->extBody)){
			$extBody = preg_replace(array("/<(div|br)\\s*\\/?>/i", "/<[^>]+>/"), array("\r\n", ""), $this->body);
		}else{
			$extBody = $this->extBody;
		}
		$suffix = "\n--".$this->boundary."--\n";
				
		// メールヘッダを作成
		$this->sendRaw($this->from, $this->fromAddress, $this->to, $this->subject, $contentType, $extBodyHead.trim(mb_convert_encoding($extBody, "JIS", "UTF-8")).$this->prefix.$this->qp_encode(mb_convert_encoding($this->body, "JIS", "UTF-8")).$suffix);
		
		try{
			// 決済方法のテーブルモデルを読み込み
			LoadTable("MaillogsTable");
			$maillogs = new MaillogsTable();
					
			// データベースINSERTモデルの読み込み
			$insert = new DatabaseInsert($maillogs, $this->db);
			
			// 設定するデータ配列を定義
			$values = array();
			$values["mail_from"] = $this->fromAddress;
			$values["mail_to"] = $this->toAddress;
			$values["subject"] = $this->subject;
			$values["body"] = $this->prefix.$this->body.$suffix;
			$values["mail_time"] = date("Y-m-d H:i:s");
			
			// INSERTの実行
			$insert->execute($values);
		}catch(Exception $e){
			// メールログの書き込み失敗はエラーと見なさない。
		}
	}
	
    /**
	 * HTMLメールを返信します。
	 * 送信時、送信元と送信先が入れ替わります。
	 *
	 * @parmas string $contentType メール全体のコンテンツタイプ。マルチパートで送信するこのクラスでは原則使用しない。
	 * @params string $suffix メールの最後に付加する文字列メール毎に違う文章を設定する際に利用。
     * @access public
     */
	public function reply($contentType = "", $suffix = ""){
		// コンテンツタイプを設定
		if(empty($contentType)){
			$contentType = "multipart/alternative;boundary=\"".$this->boundary."\"";
		}
		
		// 代替テキストを設定
		$extBodyHead = "\n--".$this->boundary."\n";
		$extBodyHead .= "Content-Type: text/plain; charset=\"iso-2022-jp\"\n";
		$extBodyHead .= "Content-Transfer-Encoding: 7bit\n\n";
		if(empty($this->extBody)){
			$extBody = preg_replace(array("/<(div|br)\\s*\\/?>/i", "/<[^>]+>/"), array("\r\n", ""), $this->body);
		}else{
			$extBody = $this->extBody;
		}
		$suffix = "\n--".$this->boundary."--\n";
				
		// メールヘッダを作成
		$this->sendRaw($this->to, $this->toAddress, $this->from, $this->subject, $contentType, $extBodyHead.trim(mb_convert_encoding($extBody, "JIS", "UTF-8")).$this->prefix.$this->qp_encode(mb_convert_encoding($this->body, "JIS", "UTF-8")).$suffix);
	}
}

/**
 * HTML形式のメール送信に使用するクラスです。
 *
 * @package Common
 * @author Naohisa Minagawa <info@sweetberry.jp>
 */
class SendHtmlMail extends SendMail{
	/**
	 * プレフィックス１
	 * これはマルチパートのエンベロープ部分の為に使用します。
	 */
	private $prefix;
	
	/**
	 * プレフィックス２
	 * これはHTMLパートのヘッダ部分の為に使用します。
	 */
	private $prefix2;
	
	/**
	 * HTMLメールを読めないクライアントのための代替テキスト
	 */
	private $extBody;

	/**
	 * マルチパート用区切り文字列
	 * multipart/mixedに使用します。
	 */
	private $boundary1;
	
	/**
	 * マルチパート用区切り文字列
	 * multipart/relatedに使用します。
	 */
	private $boundary2;
	
	/**
	 * マルチパート用区切り文字列
	 * multipart/alternativeに使用します。
	 */
	private $boundary3;

	/**
	 * 画像識別用キーに指定するサフィックス
	 */
	private $imageKey;

	/**
	 * 画像データの配列
	 */
	private $images;
	
    /**
	 * コンストラクタです。HTMLメールのための初期設定を行います。
	 *
     * @access public
     */
	public function __construct(){
		parent::__construct();
		
		$this->extBody = "";
		
		$this->images = array();
		$this->imageKey = date("YmdHis");

		// マルチパートのバウンダリを設定
		$this->boundary1 = "MINES-" . uniqid("b");
		$this->boundary2 = "MINES-" . uniqid("b")."_01";
		$this->boundary3 = "MINES-" . uniqid("b")."_02";

		// Bodyのヘッダ部分を設定
		$this->prefix = "\n--".$this->boundary1."\n";
		$this->prefix .= "Content-Type: multipart/related;boundary=\"".$this->boundary2."\"\n";
		$this->prefix .= "\n--".$this->boundary2."\n";
		$this->prefix .= "Content-Type: multipart/alternative;boundary=\"".$this->boundary3."\"\n";
		$this->prefix2 = "\n--".$this->boundary3."\n";
		$this->prefix2 .= "Content-Type: text/html; charset=\"iso-2022-jp\"\n";
		$this->prefix2 .= "Content-Transfer-Encoding: quoted-printable\n\n";
	}
	
    /**
	 * HTMLメールが読めないクライアントのための代替テキストを設定します。
	 *
	 * @params string $parts 代替テキストとして追加指定する文字列
     * @access public
     */
	public function addExtBody($parts){
		$this->extBody .= $parts;
	}
	
    /**
	 * HTMLメールに画像リンクを追加します。
	 *
	 * @params binary $data 画像データ
     * @access public
     */
	public function addImage($data){
		$this->images[] = $data;
		$this->addBody("<IMG src=\"cid:".sprintf("%02d", count($this->images))."@".$this->imageKey."\">");
	}
	
    /**
	 * HTMLメールを送信します。
	 *
	 * @parmas string $contentType メール全体のコンテンツタイプ。マルチパートで送信するこのクラスでは原則使用しない。
	 * @params string $suffix メールの最後に付加する文字列メール毎に違う文章を設定する際に利用。
     * @access public
     */
	public function send($contentType = "", $suffix = ""){
		// コンテンツタイプを設定
		if(empty($contentType)){
			$contentType = "multipart/mixed;boundary=\"".$this->boundary1."\"";
		}
		
		// 代替テキストを設定
		$extBodyHead = "\n--".$this->boundary3."\n";
		$extBodyHead .= "Content-Type: text/plain; charset=\"iso-2022-jp\"\n";
		$extBodyHead .= "Content-Transfer-Encoding: 7bit\n\n";
		if(empty($this->extBody)){
			$extBody = preg_replace(array("/<(div|br)\\s*\\/?>/i", "/<[^>]+>/"), array("\r\n", ""), $this->body);
		}else{
			$extBody = $this->extBody;
		}
		
		// 添付画像を設定
		$suffix .= 	"\n--".$this->boundary3."--\n";
		foreach($this->images as $index => $imageData){
			$suffix .= "\n--".$this->boundary2."\n";
			if ( preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $imageData) )  {
				$suffix .= "Content-Type: image/png; name=\"image.png\"\n";
			} elseif ( preg_match( '/^GIF8[79]a/', $imageData) )  {
				$suffix .= "Content-Type: image/gif; name=\"image.gif\"\n";
			} elseif ( preg_match( '/^\xff\xd8/', $imageData) )  {
				$suffix .= "Content-Type: image/jpeg; name=\"image.jpg\"\n";
			}
			$suffix .= "Content-Transfer-Encoding: base64\n";
			$suffix .= "Content-ID: <".sprintf("%02d", ($index + 1))."@".$this->imageKey.">\n\n";
			$suffix .= chunk_split(base64_encode($imageData));
			$suffix .= "\n--".$this->boundary2."--\n";
		}
		$suffix .= "\n--".$this->boundary1."--\n";
		
		// メールヘッダを作成
		$this->sendRaw($this->from, $this->fromAddress, $this->to, $this->subject, $contentType, $this->prefix.$extBodyHead.trim(mb_convert_encoding($extBody, "JIS", "UTF-8")).$this->prefix2.$this->qp_encode(mb_convert_encoding($this->body, "JIS", "UTF-8")).$suffix);
		
		try{
			// 決済方法のテーブルモデルを読み込み
			LoadTable("MaillogsTable");
			$maillogs = new MaillogsTable();
					
			// データベースINSERTモデルの読み込み
			$insert = new DatabaseInsert($maillogs, $this->db);
			
			// 設定するデータ配列を定義
			$values = array();
			$values["mail_from"] = $this->fromAddress;
			$values["mail_to"] = $this->toAddress;
			$values["subject"] = $this->subject;
			$values["body"] = $this->prefix.$this->body.$suffix;
			$values["mail_time"] = date("Y-m-d H:i:s");
			
			// INSERTの実行
			$insert->execute($values);
		}catch(Exception $e){
			// メールログの書き込み失敗はエラーと見なさない。
		}
	}
	
    /**
	 * HTMLメールを返信します。
	 * 送信時、送信元と送信先が入れ替わります。
	 *
	 * @parmas string $contentType メール全体のコンテンツタイプ。マルチパートで送信するこのクラスでは原則使用しない。
	 * @params string $suffix メールの最後に付加する文字列メール毎に違う文章を設定する際に利用。
     * @access public
     */
	public function reply($contentType = "", $suffix = ""){
		// コンテンツタイプを設定
		if(empty($contentType)){
			$contentType = "multipart/mixed;boundary=\"".$this->boundary1."\"";
		}
		
		// 代替テキストを設定
		$extBody = "\n--".$this->boundary3."\n";
		$extBody .= "Content-Type: text/plain; charset=\"iso-2022-jp\"\n";
		$extBody .= "Content-Transfer-Encoding: 7bit\n\n";
		if(empty($this->extBody)){
			$extBody = preg_replace("/<[^>]>/", "", $this->body);
		}else{
			$extBody = $this->extBody;
		}
		
		// 添付画像を設定
		$suffix .= 	"\n--".$this->boundary3."--\n";
		foreach($this->images as $index => $imageData){
			$suffix .= "\n--".$this->boundary2."\n";
			if ( preg_match( '/^\x89PNG\x0d\x0a\x1a\x0a/', $imageData) )  {
				$suffix .= "Content-Type: image/png; name=\"image.png\"\n";
			} elseif ( preg_match( '/^GIF8[79]a/', $imageData) )  {
				$suffix .= "Content-Type: image/gif; name=\"image.gif\"\n";
			} elseif ( preg_match( '/^\xff\xd8/', $imageData) )  {
				$suffix .= "Content-Type: image/jpeg; name=\"image.jpg\"\n";
			}
			$suffix .= "Content-Transfer-Encoding: base64\n";
			$suffix .= "Content-ID: <".sprintf("%02d", ($index + 1))."@".$this->imageKey.">\n\n";
			$suffix .= chunk_split(base64_encode($imageData));
			$suffix .= "\n--".$this->boundary2."--\n";
		}
		$suffix .= "\n--".$this->boundary1."--\n";
		
		// メールヘッダを作成
		$this->sendRaw($this->to, $this->toAddress, $this->from, $this->subject, $contentType, $this->prefix.mb_convert_encoding($extBody, "JIS", "UTF-8").$this->prefix2.$this->qp_encode(mb_convert_encoding($this->body, "JIS", "UTF-8")).$suffix);
	}
}
?>
