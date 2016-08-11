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

    protected $temp_table = false;

    protected $auto_increment = null;

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

    public function __construct (string $table_name, DBDriver $db_driver) {
        $this->table_name = $table_name;
    }

    public function setEngine (string $engine_name) {
        $found = false;
        $founded_engine = "";

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
        $this->db_engine = $engine_name;
    }

    public function setCharset (string $charset) {
        $this->charset = utf8_encode(htmlentities($charset));
    }

    public function setTmpTable (bool $tmp_table) {
        $this->temp_table = $tmp_table;
    }

    public function setAutoIncrementStartValue (int $value) {
        $this->auto_increment = $value;
    }

    /**
     * add integer column
     *
     * @param $name name of column
     * @param $length length of column
     * @param $unsigned unsigned value false / true
     * @param $zerofill true, if values should be filled with 0, if value length isnt length of column
     */
    public function addInt (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "int",
            'name' => $name,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    /**
     * addInteger() is an alias to addInt()
     */
    public function addInteger (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->addInt($name, $length, $not_null, $auto_increment, $default_value, $unsigned, $zerofill);
    }

    /**
     * add unsigned integer column
     *
     * @param $name name of column
     * @param $length length of column
     * @param $zerofill true, if values should be filled with 0, if value length isnt length of column
     */
    public function addUnsignedInteger (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "int",
            'name' => $name,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => true,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function generateCreateQuery () : string {
        $tmp_str = "";

        if ($this->temp_table) {
            $tmp_str = "TEMPORARY ";
        }

        //http://dev.mysql.com/doc/refman/5.7/en/create-table.html
        $sql = "CREATE " . $tmp_str . " TABLE `{DBPRAEFIX}" . $this->escape($this->table_name) . "` IF NOT EXISTS (\r\n";

        //add coloums
        $sql .= $this->generateColoumQuery();

        $sql .= ")";

        if (!empty($this->db_engine)) {
            //add database engine
            $sql .= " TYPE=" . $this->db_engine;
        }

        //add auto increment value
        if ($this->auto_increment != null) {
            $sql .= " AUTO_INCREMENT=" . (int) $this->auto_increment;
        }

        if (!empty($this->charset)) {
            //add default charset
            $sql .= " DEFAULT CHARSET=" . $this->charset;
        }

        $sql .= ";";

        return $sql;
    }

    protected function generateColoumQuery () : string {
        //generate lines of coloum definitions
        $lines = $this->getColoumLines();

        //build sql query string
        return implode(",\r\n", $lines) . "\r\n";
    }

    protected function getColoumLines () : array {
        $lines = array();

        foreach ($this->columns as $column) {
            $line = "`" . $column['name'] . "` ";

            $length_str = "";
            $not_null_str = "";
            $default_str = "";

            if (isset($column['length']) && $column['length'] != null) {
                $length_str = "(" . (int) $column['length'] . ")";
            }

            if (isset($column['default']) && $column['default'] != null) {
                $default_str = " DEFAULT '" . $column['default'] . "'";
            }

            switch ($column['type']) {
                case 'int':
                    //Integer
                    $line .= "INT" . $length_str . $not_null_str;

                    //add AUTO_INCREMENT if neccessary
                    if ($column['auto_increment'] == true) {
                        $line .= " AUTO_INCREMENT";
                    }

                    //add DEFAULT '<value>' if neccessary
                    $line .= $default_str;

                    if ($column['unsigned'] == true) {
                        $line .= " UNSIGNED";
                    }

                    if ($column['zerofill'] == true) {
                        $line .= " ZEROFILL";
                    }

                    break;

                default:
                    throw new UnsupportedDataTypeException("MySQL data type " . $column['type'] . " isnt supported yet.");
            }

            $lines[] = $line;
        }

        return $lines;
    }

    public function escape (string $str) {
        return utf8_encode(htmlentities($str));
    }

    public static function listTables (DBDriver $dbDriver) {
        return $dbDriver->listRows("SHOW TABLES; ");
    }

    public static function getTableStructure (string $table_name, DBDriver $dbDriver) {
        //https://dev.mysql.com/doc/refman/5.5/en/creating-tables.html
        return $dbDriver->listRows("DESCRIBE `{DBPRAEFIX}" . $table_name . "`; ");
    }

}