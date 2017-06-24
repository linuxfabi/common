<?php
class Debug extends Database {
    public  $database = false;
    
    public function __construct($database = '') {
        $this->database = (empty($database)) ? $this->base : $database;
        $this->connect();
    }
    
    public function createTable() {
        $qry = "CREATE TABLE `debug`(
                    `debugPk` INT UNSIGNED NOT NULL AUTO_INCREMENT, 
                    `name` VARCHAR(32) NOT NULL, 
                    `file` VARCHAR(32) NOT NULL, 
                    `output` BLOB, 
                    `itime` TIMESTAMP NOT NULL, 
                    PRIMARY KEY (`debugPk`)
                )"; 
        $this->doQuery($qry);
    }
    
    public function emptyTable() {
        $qry = "TRUNCATE TABLE `debug`"; 
        $this->doQuery($qry);
    }
    
    public function dropTable() {
        $qry = "DROP TABLE `debug`"; 
        $this->doQuery($qry);
    }
    
    public function log($name, $file, $output) {
        $DModel   = new Model('debug');
        $DModel->{'name'}   = $name;
        $DModel->{'file'}   = $file;
        $DModel->{'output'} = var_export($output, true);
        
        $DModel->save();
        
        var_dump($DModel);
    }






}
?>