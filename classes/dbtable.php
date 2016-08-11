<?php

/**
 * Database table
 */
class DBTable {

    /**
     * name of database table
     */
    protected $table_name = "";

    /**
     * coloums structure of database table
     */
    protected $columns = array();

    public function __construct ($table_name, DBDriver $db_driver) {
        $this->table_name = $table_name;
    }

    public function addInt ($name, $length = 10) {
        //
    }

    public function generateCreateQuery () : string {
        $sql = "CREATE TABLE `{DBPRAEFIX}" . $this->escape($this->table_name) . "` IF NOT EXISTS (";

        $sql .= ");";

        return $sql;
    }

    public function escape ($str) {
        return utf8_encode(htmlentities($str));
    }

    public static function listTables (DBDriver $dbDriver) {
        return $dbDriver->listRows("SHOW TABLES; ");
    }

    public static function getTableStructure ($table_name, DBDriver $dbDriver) {
        //https://dev.mysql.com/doc/refman/5.5/en/creating-tables.html
        return $dbDriver->listRows("DESCRIBE `{DBPRAEFIX}" . $table_name . "`; ");
    }

}