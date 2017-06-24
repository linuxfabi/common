<?php

class Mail {
    
    private $toList     = array();
    private $ccList     = array();
    private $bccList    = array();
    private $from       = '';
    private $subject    = '';
    private $header     = '';
    private $textMsg    = '';
    private $htmlMsg    = '';
    private $mailHeader = '';
    
    function __construct() {}
    
    // setter
    public function addTo($to)           {$this->toList[]   = $to;}
    public function addCC($cc)           {$this->ccList[]   = $cc;}
    public function addBCC($bcc)         {$this->bccList[]  = $bcc;}
    public function setFrom($from)       {$this->from       = $from;}
    public function setSubject($subject) {$this->subject    = $subject;}
    public function setHeader($header)   {$this->mailHeader = $header;}
    public function setTextMsg($textMsg) {$this->textMsg    = $textMsg;}
    public function setHTMLMsg($htmlMsg) {$this->htmlMsg    = $htmlMsg;}
    
    // load textbody from file
    public function loadTextFile($path) {
        if (is_file($path)) {
            $this->setTextMsg(file_get_contents($path));
        }
    }
    
    // load html body from file
    public function loadHTMLFile($path) {
        if (is_file($path)) {
            $this->setHTMLMsg(file_get_contents($path));
        }
    }
    
    // build the mail header
    private function _buildHeader() {
        $header  = 'MIME-Version: 1.0'."\r\n";
        $header .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
        $header .= 'To: ';
        foreach ($this->toList as $curTo) {
            $header .= $curTo.',';
        }
        $header  = substr($header, 0, -1);
        $header .= "\r\n";
        $header .= 'From: '.$this->from."\r\n";
        $header .= 'CC: ';
        foreach ($this->ccList as $curCC) {
            $header .= $curCC.',';
        }
        $header  = substr($header, 0, -1);
        $header .= "\r\n";
        $header .= 'BCC: ';
        foreach ($this->bccList as $curBCC) {
            $header .= $curBCC.',';
        }
        $header  = substr($header, 0, -1);
        $header .= "\r\n\r\n";
        $this->setHeader($header);
    }
    
    // send function
    public function send() {
        if (empty($this->mailHeader)) {
            $this->_buildHeader();
        }
        
        if (!empty($this->toList)) {
          foreach($this->toList as $curTo) {
            mail($curTo, $this->subject, $this->textMsg, $this->mailHeader);
          }
        }
        
        if (!empty($this->ccList)) {
          foreach($this->ccList as $curCC) {
            mail($curCC, $this->subject, $this->textMsg, $this->mailHeader);
          }
        }
        
        if (!empty($this->bccList)) {
          foreach($this->bccList as $curBCC) {
            mail($curBCC, $this->subject, $this->textMsg, $this->mailHeader);
          }
        }
    }
}

?>