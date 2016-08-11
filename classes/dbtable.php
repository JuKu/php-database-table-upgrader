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
     * database engine, like InnoDB
     */
    protected $db_engine = "";

    /**
     * default table charset
     */
    protected $charset = "utf8";

    /**
     * list of supported database engines
     */
    protected static $supported_engines = array(
        "InnoDB",
        "MyISAM",
        "PERFORMANCE_SCHEMA",
        "MRG_MYISAM",
        "FEDERATED",
        "CSV",
        "MEMORY",
        "ARCHIVE"
    );

    /**
     * coloums structure of database table
     */
    protected $columns = array();

    public function __construct ($table_name, DBDriver $db_driver) {
        $this->table_name = $table_name;
    }

    public function setEngine ($engine_name) {
        $found = false;
        $founded_engine = "";

        echo "set engine " . $engine_name;

        foreach (self::$supported_engines as $name) {
            if (strcmp(strtolower($engine_name), strtolower($name))) {
                //database engine is supported
                $found = true;
                $founded_engine = $name;

                break;
            }
        }

        if (!$found) {
            throw new UnsupportedDBEngineException("Database engine " . $engine_name . " isnt in supported database engine list.");
        }

        //set database engine
        $this->db_engine = $name;
    }

    public function setCharset ($charset) {
        $this->charset = utf8_encode(htmlentities($charset));
    }

    public function addInt ($name, $length = 10) {
        $this->columns[] = array(
            'type' => "int",
            'length' => $length
        );
    }

    public function generateCreateQuery () : string {
        $sql = "CREATE TABLE `{DBPRAEFIX}" . $this->escape($this->table_name) . "` IF NOT EXISTS (\r\n";

        //add coloums
        $sql .= $this->generateColoumQuery();

        $sql .= ")";

        if (!empty($this->db_engine)) {
            //add database engine
            $sql .= " TYPE=" . $this->db_engine;
        }

        if (!empty($this->charset)) {
            //add default charset
            $sql .= " DEFAULT CHARSET=" . $this->charset;
        }

        $sql .= ";";

        return $sql;
    }

    protected function generateColoumQuery () : string {
        $lines = array();

        return implode(",\r\n", $lines);
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