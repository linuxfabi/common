<?php 

abstract class Database extends DBConfig {
	
	protected $con    = null;
	public    $querys = array();
	public    $errors = array();
	
	
	function __construct($base='') {
		if (!empty($base)) $this->base = $base;
		$this->connect();
	}
	
	
	
	protected function connect() {
	  // mysql_pconnect is marked as deprecated
	  // This could solve future problems
	  //mysqli_connect($this->serv, $this->user, $this->pass, $this->base);
		$this->con = mysql_pconnect($this->serv, $this->user, $this->pass);
		if ($this->con) mysql_select_db($this->base, $this->con);
		else die(mysql_error);
	}
	
	protected function doQuery($qry, $paramList=array()) {
		if (!empty($paramList)) {
			$paramList = array_map('mysql_real_escape_string', $paramList);
			$qry       = vsprintf($qry, $paramList);
		}
        $this->querys[] = $qry;
		if (!strstr($qry, ';')) {
			$res = mysql_query($qry, $this->con);
			if (mysql_error($this->con) != '') {
			    $this->errors[] = mysql_error($this->con);
			}
			return $res;
		} else {
			$qry    = explode(';', $qry);
			$result = array();
			foreach ($qry AS $query) {
				if (!empty($query)) {
					$result[] = mysql_query($query, $this->con);
                    if (mysql_error($this->con) != '') {
                        $this->errors[] = mysql_error($this->con);
                    }
				}
			}
			return $result;
		}
	}
	
	
		
}

?>