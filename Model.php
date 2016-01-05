<?php

class Model extends Database {
	
	// init object and colect data about the table
	function __construct($table, $database='') {
		parent::__construct($database);
		$this->table = $table;
		$this->getFields();
		if (!isset($this->pk)) die("Modelcheckup not aproved please check table '$this->table' has a primarykey"); // if init fail call selfdestruction
	}
	
	
	
	// get fieldinfo
	public function getFields() {
		$res = $this->doQuery('SHOW COLUMNS FROM `%s`', array($this->table));
		if (!$res || mysql_error()) die("SHOW COLUMNS failed please check table '$this->table' exists");
		while ($field = mysql_fetch_array($res, MYSQL_ASSOC)) {
			$this->_addField($field);
		}
	}
	
	
	// Identify field and save info to object
    private function _addField($field) {
	    // parse and identify type
	    $type = $field['Type'];
	    if (is_int(strpos($type, '('))) {
	        $type = explode('(', $type);
            $fieldInfo['type'] = $type[0];
            switch ($fieldInfo['type']) {
                case 'enum':
                    $type[1] = array_shift(explode(')', $type[1]));
                    $type[1] = str_replace("'", '', $type[1]);
                    $fieldInfo['enum'] = explode(",", $type[1]);
                    break;
                default:
                    $fieldInfo['length'] = $type[1];
            }
        } else {
            $fieldInfo['type'] = $type;
        }
		
        // save colected data to object
		$fieldInfo['name'] = $field['Field'];
		if ($field['Key'] == 'PRI') $this->pk = $fieldInfo['name'];
		else $this->{"field_{$fieldInfo['name']}"} = $fieldInfo;
    }
	
	
	
	// ---------------------------------------------------------------------
	// Querybuilder - Section
	// ---------------------------------------------------------------------
	
	// Select querys -------------------------------------------------------
	public function getRow($id) {
		return $this->getRowsByField($this->pk, $id);
	}
	
	public function getRowsByField($fieldName, $value) {
	    $qry     = "WHERE %s = '%s'";
	    $param[] = $fieldName;
		$param[] = $value;
		return $this->getRowsByCriteria($qry, $param);
	}
	
	public function getAllRows() {
		return $this->getRowsByCriteria('', array());
	}
	
	public function getRowsByCriteria($qry, $param) {
		$qry     = "SELECT * FROM `%s` $qry";
        $param   = array_merge((array)$this->table, $param);
        $res = $this->doQuery($qry, $param);
        if (!$res || mysql_error()) return false;
        $rows = array();
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
	}
	
	public function checkRowExists($col, $val) {
	    $qry     = "WHERE `%s` = `%s`";
	    $param[] = $col;
	    $param[] = $val;
	    $res     = $this->getRowsByCriteria($qry, $param);
	    return ($res) ? true : false;
	}
	
	
	// Insert/Update querys -------------------------------------------------
	public function save() {
		// build queryheader
		if (!isset($this->{$this->pk})) $qry = 'INSERT INTO `%s` ';
		else $qry = 'UPDATE `%s` ';
		$param[] = $this->table;
		$qry .= 'SET ';
		
		// search for valid vars and build queryfildpart
		$varList = get_object_vars($this);
		foreach ($varList as $varName => $curVar) {
                        
            if (!is_null($curVar)) {
                        
                if (isset($varList["field_$varName"])) {
                    $qry .= "%s = '%s', ";
                    $param[] = $varName;
                    $param[] = $curVar;
                }
            }
		}
		$qry = substr($qry, 0, -2); //remove last ", "
	
		// add pk if update
		if (isset($this->{$this->pk})) {
			$qry .= 'WHERE %s = %d';
			$param[] = $this->pk;
			$param[] = $this->{$this->pk};
		}
		
		// do it
		$this->doQuery($qry, $param);
		if (mysql_error()) return -1;
		return isset($this->{$this->pk}) ? mysql_affected_rows() : $this->getLastInsertId();
	}
	
	
	
	// Delete querys --------------------------------------------------------
	public function deleteRow($id) {
		$qry     = "DELETE FROM `%s` WHERE %s = %d LIMIT 1";
		$param[] = $this->table;
		$param[] = $this->pk;
		$param[] = $id;
		$this->doQuery($qry, $param);
		if (mysql_error()) return false;
		return (mysql_affected_rows() > 0) ? true : false;
	}
	
	
	// Additional querys ----------------------------------------------------
	public function getLastInsertId() {
		return mysql_result($this->doQuery('SELECT LAST_INSERT_ID()'), 0);
	}
}

?>