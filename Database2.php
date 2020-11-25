<?php 

abstract class Database extends DBConfig {
	
	protected $con     = null;
	public    $lastRes = null;
	public    $querys  = array();
	public    $errors  = array();
	
	
	function __construct($base='') {
		if (!empty($base)) $this->base = $base;
		$this->connect();
	}
	
	
	
	protected function connect() {
		$this->con = mysqli_connect($this->serv, $this->user, $this->pass, $this->base);
		if (!$this->con) die(mysqli_connect_error());
	}
	
	protected function doQuery($qry, $paramList=array()) {
		if (!empty($paramList)) {
			//$paramList = array_map('mysqli_real_escape_string', $paramList);
			$qry       = vsprintf($qry, $paramList);
		}
        $this->querys[] = $qry;
		if (!strstr($qry, ';')) {
			$this->lastRes = mysqli_query($this->con, $qry);
			if (mysqli_error($this->con) != '') $this->errors[] = mysqli_error();
			return $this->lastRes->fetch_assoc();
		} else {
			$qry    = explode(';', $qry);
			$result = array();
			foreach ($qry AS $query) {
				if (!empty($query)) {
				  $this->lastRes = mysqli_query($this->con, $query);
				  $result[]      = $this->lastRes->fetch_assoc();
          if (mysqli_error() != '') $this->errors[] = mysqli_error($this->con);
				}
			}
			return $result;
		}
	}
	
	
		
}

?>