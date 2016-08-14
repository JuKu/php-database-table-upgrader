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

    /**
     * indexes of table
     */
    protected $indexes = array();

    protected $db_driver = null;

    public function __construct (string $table_name, DBDriver &$db_driver) {
        $this->table_name = $this->escape($table_name);
        $this->db_driver = &$db_driver;
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

    public function addVarchar (string $name, int $length = 255, bool $not_null = false, string $default_value = null, bool $binary = false, string $charset = null) {
        $this->columns[] = array(
            'type' => "varchar",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value,
            'charset' => $charset,
            'binary' => $binary,
            'length' => $length
        );
    }

    public function addBit (string $name, int $length = null, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "bit",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value,
            'length' => $length
        );
    }

    public function addBinary (string $name, int $length = null, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "binary",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value,
            'length' => $length
        );
    }

    public function addText (string $name, bool $not_null = false, string $default_value = null, bool $binary = false, string $charset = null) {
        $this->columns[] = array(
            'type' => "text",
            'name' => $name,
            'not_null' => $not_null,
            'binary' => $binary,
            'charset' => $charset,
            'default' => $default_value
        );
    }

    public function addChar (string $name, int $length = 255, bool $not_null = false, string $default_value = null, bool $binary = false, string $charset = null) {
        $this->columns[] = array(
            'type' => "char",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value,
            'charset' => $charset,
            'binary' => $binary,
            'length' => $length
        );
    }

    public function addTinyText (string $name, bool $not_null = false, string $default_value = null, bool $binary = false, string $charset = null) {
        $this->columns[] = array(
            'type' => "tinytext",
            'name' => $name,
            'not_null' => $not_null,
            'binary' => $binary,
            'charset' => $charset,
            'default' => $default_value
        );
    }

    public function addMediumText (string $name, bool $not_null = false, string $default_value = null, bool $binary = false, string $charset = null) {
        $this->columns[] = array(
            'type' => "mediumtext",
            'name' => $name,
            'not_null' => $not_null,
            'binary' => $binary,
            'charset' => $charset,
            'default' => $default_value
        );
    }

    public function addLongText (string $name, bool $not_null = false, string $default_value = null, bool $binary = false, string $charset = null) {
        $this->columns[] = array(
            'type' => "longtext",
            'name' => $name,
            'not_null' => $not_null,
            'binary' => $binary,
            'charset' => $charset,
            'default' => $default_value
        );
    }

    public function addTinyInt (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "tinyint",
            'name' => $name,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addSmallInt (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "smallint",
            'name' => $name,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addMediumInt (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "mediumint",
            'name' => $name,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addBigInt (string $name, int $length = null, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "bigint",
            'name' => $name,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addDecimal (string $name, int $length = 5, int $decimals = 2, bool $not_null = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        //DECIMAL doesnt support AUTO_INCREMENT

        $this->columns[] = array(
            'type' => "decimal",
            'name' => $name,
            'decimals' => $decimals,
            'not_null' => $not_null,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addNumeric (string $name, int $length = 5, int $decimals = 2, bool $not_null = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        //NUMERIC doesnt support AUTO_INCREMENT

        $this->columns[] = array(
            'type' => "numeric",
            'name' => $name,
            'decimals' => $decimals,
            'not_null' => $not_null,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addDouble (string $name, int $length = 5, int $decimals = 2, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "double",
            'name' => $name,
            'decimals' => $decimals,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addFloat (string $name, int $length = 5, int $decimals = 2, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "float",
            'name' => $name,
            'decimals' => $decimals,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addReal (string $name, int $length = 5, int $decimals = 2, bool $not_null = false, bool $auto_increment = false, int $default_value = null, bool $unsigned = false, bool $zerofill = false) {
        $this->columns[] = array(
            'type' => "real",
            'name' => $name,
            'decimals' => $decimals,
            'not_null' => $not_null,
            'auto_increment' => $auto_increment,
            'default' => $default_value,
            'unsigned' => $unsigned,
            'zerofill' => $zerofill,
            'length' => $length
        );
    }

    public function addBlob (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "blob",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addTinyBlob (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "tinyblob",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addMediumBlob (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "mediumblob",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addLongBlob (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "longblob",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addEnum (string $name, array $values = array(), bool $not_null = false, string $default_value = null, string $charset = null) {
        $this->columns[] = array(
            'type' => "enum",
            'name' => $name,
            'values' => $values,
            'not_null' => $not_null,
            'default' => $default_value,
            'charset' => $charset
        );
    }

    public function addSet (string $name, array $values = array(), bool $not_null = false, string $default_value = null, string $charset = null) {
        $this->columns[] = array(
            'type' => "set",
            'name' => $name,
            'values' => $values,
            'not_null' => $not_null,
            'default' => $default_value,
            'charset' => $charset
        );
    }

    public function addDate (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "date",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addTime (string $name, bool $not_null = false, string $default_value = null, int $fsp = null) {
        $this->columns[] = array(
            'type' => "time",
            'name' => $name,
            'not_null' => $not_null,
            'fsp' => $fsp,
            'default' => $default_value
        );
    }

    public function addYear (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "year",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addJSON (string $name, bool $not_null = false, string $default_value = null) {
        $this->columns[] = array(
            'type' => "json",
            'name' => $name,
            'not_null' => $not_null,
            'default' => $default_value
        );
    }

    public function addTimestamp (string $name, bool $not_null = false, string $default_value = null, bool $on_update_current_timestamp = false, int $fsp = null) {
        $this->columns[] = array(
            'type' => "timestamp",
            'name' => $name,
            'not_null' => $not_null,
            'on_update_current_timestamp' => $on_update_current_timestamp,
            'fsp' => $fsp,
            'default' => $default_value
        );
    }

    public function addDateTime (string $name, bool $not_null = false, string $default_value = null, int $fsp = null) {
        $this->columns[] = array(
            'type' => "datetime",
            'name' => $name,
            'not_null' => $not_null,
            'fsp' => $fsp,
            'default' => $default_value
        );
    }

    public function addPrimaryKey ($columns) {
        $this->indexes['primary'] = array(
            'type' => "primary",
            'columns' => $columns
        );
    }

    public function addIndex ($columns, string $index_name = null) {
        if ($index_name == null) {
            if (!is_array($columns)) {
                $index_name = "ix_" . $columns;
            } else {
                throw new UnsupportedDataTypeException("Multi Column indexes require an name! addIndex(<columns>, <index index>)");
                //$index_name = "ix_" . md5(serialize($columns))
            }
        }

        $this->indexes[$index_name] = array(
            'type' => "index",
            'name' => $index_name,
            'columns' => $columns
        );
    }

    public function addUnique ($columns, string $index_name = null) {
        if ($index_name == null) {
            if (!is_array($columns)) {
                $index_name = "uq_" . $columns;
            } else {
                throw new UnsupportedDataTypeException("Multi Column indexes require an name! addUnique(<columns>, <index index>)");
                //$index_name = "ix_" . md5(serialize($columns))
            }
        }

        $this->indexes[$index_name] = array(
            'type' => "unique",
            'name' => $index_name,
            'columns' => $columns
        );
    }

    public function addSpatial ($columns, string $index_name = null) {
        if ($index_name == null) {
            if (!is_array($columns)) {
                $index_name = "sp_" . $columns;
            } else {
                throw new UnsupportedDataTypeException("Multi Column indexes require an name! addUnique(<columns>, <index index>)");
                //$index_name = "ix_" . md5(serialize($columns))
            }
        }

        $this->indexes[$index_name] = array(
            'type' => "spatial",
            'name' => $index_name,
            'columns' => $columns
        );
    }

    public function addFulltext ($columns, string $index_name = null) {
        if ($index_name == null) {
            if (!is_array($columns)) {
                $index_name = "ix_" . $columns;
            } else {
                throw new UnsupportedDataTypeException("Multi Column indexes require an name! addFulltext(<columns>, <index index>)");
                //$index_name = "ix_" . md5(serialize($columns))
            }
        }

        $this->indexes[$index_name] = array(
            'type' => "fulltext",
            'name' => $index_name,
            'columns' => $columns
        );
    }

    public function addForeignKey ($columns, string $reference_table_name, $reference_columns, string $index_name = null, string $on_update = null, string $on_delete = null) {
        if ($index_name == null) {
            if (!is_array($columns)) {
                $index_name = "ix_" . $columns;
            } else {
                throw new UnsupportedDataTypeException("Multi Column indexes require an name! addFulltext(<columns>, <index index>)");
                //$index_name = "ix_" . md5(serialize($columns))
            }
        }

        $this->indexes[$index_name] = array(
            'type' => "foreign",
            'name' => $index_name,
            'reference_table_name' => $reference_table_name,
            'reference_columns' => $reference_columns,
            'on_update' => $on_update,
            'on_delete' => $on_delete,
            'columns' => $columns
        );
    }

    public function generateCreateQuery () : string {
        $tmp_str = "";

        if ($this->temp_table) {
            $tmp_str = " TEMPORARY";
        }

        //http://dev.mysql.com/doc/refman/5.7/en/create-table.html
        $sql = "CREATE" . $tmp_str . " TABLE IF NOT EXISTS `{DBPRAEFIX}" . $this->escape($this->table_name) . "` (\r\n";

        //add coloums
        $sql .= $this->generateColoumQuery();

        //add indexes
        $sql .= $this->generateIndexQuery();

        $sql .= "\r\n)";

        if (!empty($this->db_engine)) {
            //add database engine
            $sql .= " ENGINE=" . $this->db_engine;
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
        return implode(",\r\n", $lines) . "";
    }

    protected function generateIndexQuery () : string {
        //check, if no index is set
        if (count($this->indexes) == 0) {
            return "";
        }

        $lines = array();

        foreach ($this->indexes as $name=>$params) {
            switch ($params['type']) {
                case 'primary':
                    //PRIMARY KEY
                    $columns_str = "";

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $columns_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $columns_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    $lines[] = "PRIMARY KEY (" . $columns_str . ")";

                    break;

                case 'index':
                    //INDEX
                    $columns_str = "";

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $columns_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $columns_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    $lines[] = "INDEX `" . $params['name'] . "`(" . $columns_str . ")";

                    break;

                case 'unique':
                    //INDEX
                    $columns_str = "";

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $columns_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $columns_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    if (empty($params['name'])) {
                        $lines[] = "UNIQUE (" . $columns_str . ")";
                    } else {
                        $lines[] = "UNIQUE `" . $params['name'] . "`(" . $columns_str . ")";
                    }

                    break;

                case 'spatial':
                    //INDEX
                    $columns_str = "";

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $columns_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $columns_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    if (empty($params['name'])) {
                        $lines[] = "SPATIAL (" . $columns_str . ")";
                    } else {
                        $lines[] = "SPATIAL `" . $params['name'] . "`(" . $columns_str . ")";
                    }

                    break;

                case 'fulltext':
                    //INDEX
                    $columns_str = "";

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $columns_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $columns_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    if (empty($params['name'])) {
                        $lines[] = "FULLTEXT (" . $columns_str . ")";
                    } else {
                        $lines[] = "FULLTEXT `" . $params['name'] . "`(" . $columns_str . ")";
                    }

                    break;

                case 'foreign':
                    //INDEX
                    $columns_str = "";
                    $references_str = "";

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $columns_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $columns_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    if (is_array($params['columns'])) {
                        $columns = array();

                        foreach ($params['reference_columns'] as $column=>$params1) {
                            if (is_array($params1)) {
                                //column name with length
                                $columns[] = "`" . $params1['column'] . "`(" . (int) $params1['length'] . ")";
                            } else {
                                $columns[] = "`" . $params1 . "`";
                            }
                        }

                        $references_str = implode(", ", $columns);
                    } else {
                        //only 1 column
                        $references_str = "`" . utf8_encode(htmlentities($params['columns'])) . "`";
                    }

                    $on_update_str = "";
                    $on_delete_str = "";

                    if ($params['on_update'] != null) {
                        $on_update_str = " ON UPDATE " . strtoupper($params['on_update']);
                    }

                    if ($params['on_delete'] != null) {
                        $on_update_str = " ON DELETE " . strtoupper($params['on_delete']);
                    }

                    if (empty($params['name'])) {
                        $lines[] = "FOREIGN KEY (" . $columns_str . ") REFERENCES `" . $params['reference_table_name'] . "`(" . $references_str . ")" . $on_delete_str . $on_update_str;
                    } else {
                        $lines[] = "FOREIGN KEY `" . $params['name'] . "`(" . $columns_str . ") REFERENCES `" . $params['reference_table_name'] . "`(" . $references_str . ")" . $on_delete_str . $on_update_str;
                    }

                    break;

                default:
                    throw new UnsupportedDataTypeException("index / key isnt supported. " . serialize($params));

                    break;
            }
        }

        return ",\r\n" . implode(",\r\n", $lines);
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

            if (isset($column['not_null']) && $column['not_null'] == true) {
                $not_null_str = " NOT NULL";
            }

            if (isset($column['default']) && $column['default'] != null) {
                $default_str = " DEFAULT '" . $column['default'] . "'";
            }

            switch ($column['type']) {
                //INT
                case 'int':
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

                //VARCHAR
                case 'varchar':
                    $line .= "VARCHAR" . $length_str . $not_null_str . $default_str;

                    if ($column['binary'] == true) {
                        $line .= " BINARY";
                    }

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //TEXT
                case 'text':
                    $line .= "TEXT" . $not_null_str . $default_str;

                    if ($column['binary'] == true) {
                        $line .= " BINARY";
                    }

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //CHAR
                case 'char':
                    $line .= "CHAR" . $length_str . $not_null_str . $default_str;

                    if ($column['binary'] == true) {
                        $line .= " BINARY";
                    }

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //BIT
                case 'bit':
                    $line .= "BIT" . $length_str . $not_null_str . $default_str;
                    break;

                //BINARY
                case 'binary':
                    $line .= "BINARY" . $length_str . $not_null_str . $default_str;
                    break;

                //TINYINT
                case 'tinyint':
                    $line .= "TINYINT" . $length_str . $not_null_str;

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

                //SMALLINT
                case 'smallint':
                    $line .= "SMALLINT" . $length_str . $not_null_str;

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

                //MEDIUMINT
                case 'mediumint':
                    $line .= "SMALLINT" . $length_str . $not_null_str;

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

                //BIGINT
                case 'bigint':
                    $line .= "BIGINT" . $length_str . $not_null_str;

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

                //DECIMAL
                case 'decimal':
                    $line .= "DECIMAL(" . (int) $column['length'] . ", " . (int) $column['decimals'] . ")" . $not_null_str . $default_str;

                    if ($column['unsigned'] == true) {
                        $line .= " UNSIGNED";
                    }

                    if ($column['zerofill'] == true) {
                        $line .= " ZEROFILL";
                    }

                    break;

                //NUMERIC
                case 'numeric':
                    $line .= "NUMERIC(" . (int) $column['length'] . ", " . (int) $column['decimals'] . ")" . $not_null_str . $default_str;

                    if ($column['unsigned'] == true) {
                        $line .= " UNSIGNED";
                    }

                    if ($column['zerofill'] == true) {
                        $line .= " ZEROFILL";
                    }

                    break;

                //DOUBLE
                case 'double':
                    $line .= "DOUBLE(" . (int) $column['length'] . ", " . (int) $column['decimals'] . ")" . $not_null_str;

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

                //FLOAT
                case 'float':
                    $line .= "FLOAT(" . (int) $column['length'] . ", " . (int) $column['decimals'] . ")" . $not_null_str;

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

                //REAL
                case 'real':
                    $line .= "REAL(" . (int) $column['length'] . ", " . (int) $column['decimals'] . ")" . $not_null_str;

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

                //BLOB
                case 'blob':
                    $line .= "BLOB" . $not_null_str . $default_str;

                    break;

                //TINYTEXT
                case 'tinytext':
                    $line .= "TINYTEXT" . $not_null_str . $default_str;

                    if ($column['binary'] == true) {
                        $line .= " BINARY";
                    }

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //MEDIUMTEXT
                case 'mediumtext':
                    $line .= "MEDIUMTEXT" . $not_null_str . $default_str;

                    if ($column['binary'] == true) {
                        $line .= " BINARY";
                    }

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //LONGTEXT
                case 'longtext':
                    $line .= "LONGTEXT" . $not_null_str . $default_str;

                    if ($column['binary'] == true) {
                        $line .= " BINARY";
                    }

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //TINYBLOB
                case 'tinyblob':
                    $line .= "TINYBLOB" . $not_null_str . $default_str;

                    break;

                //MEDIUMBLOB
                case 'mediumblob':
                    $line .= "MEDIUMBLOB" . $not_null_str . $default_str;

                    break;

                //LONGBLOB
                case 'longblob':
                    $line .= "LONGBLOB" . $not_null_str . $default_str;

                    break;

                //ENUM
                case 'enum':
                    $options = array();

                    foreach ($column['values'] as $value) {
                        $options[] = "'" . $value . "'";
                    }

                    $options_str = implode(",", $options);

                    $line .= "ENUM(" . $options_str . ")" . $not_null_str . $default_str;

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //SET
                case 'set':
                    $options = array();

                    foreach ($column['values'] as $value) {
                        $options[] = "'" . $value . "'";
                    }

                    $options_str = implode(",", $options);

                    $line .= "SET(" . $options_str . ")" . $not_null_str . $default_str;

                    if ($column['charset'] != null) {
                        $line .= " CHARACTER SET " . $column['charset'];
                    }

                    break;

                //DATE
                case 'date':
                    $line .= "DATE" . $not_null_str . $default_str;

                    break;

                //TIME
                case 'time':
                    $fsp_str = "";

                    if ($column['fsp'] != null) {
                        $fsp_str = "(" . $column['fsp'] . ")";
                    }

                    $line .= "TIME" . $fsp_str . $not_null_str . $default_str;

                    break;

                //YEAR
                case 'year':
                    $line .= "YEAR" . $not_null_str . $default_str;

                    break;

                //JSON
                case 'json':
                    $line .= "JSON" . $not_null_str . $default_str;

                    break;

                //TIMESTAMP
                case 'timestamp':
                    $fsp_str = "";

                    if ($column['fsp'] != null) {
                        $fsp_str = "(" . $column['fsp'] . ")";
                    }

                    $line .= "TIMESTAMP" . $fsp_str . $not_null_str . $default_str;

                    if ($column['on_update_current_timestamp'] == true) {
                        $line .= " ON UPDATE CURRENT_TIMESTAMP";
                    }

                    break;

                //DATETIME
                case 'datetime':
                    $fsp_str = "";

                    if ($column['fsp'] != null) {
                        $fsp_str = "(" . $column['fsp'] . ")";
                    }

                    $line .= "DATETIME" . $fsp_str . $not_null_str . $default_str;

                    break;

                default:
                    throw new UnsupportedDataTypeException("MySQL data type " . $column['type'] . " isnt supported yet.");
            }

            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * create table structure in database, if table not exists
     */
    public function create () {
        //create table
        $this->db_driver->execute($this->generateCreateQuery());
    }

    /**
     * upgrades table structure in database, or if table not exists, creates table
     */
    public function upgrade () {
        //TODO: add code here
    }

    public function truncate () {
        $this->db_driver->query("TRUNCATE `" . $this->table_name . "`; ");
    }

    /**
     * alias to truncate()
     */
    public function cleanUp () {
        $this->truncate();
    }

    public function check () {
        //check table
        return $this->db_driver->getRow("CHECK TABLE `{DBPRAEFIX}" . $this->table_name . "`; ");
    }

    public function analyze () {
        //check table
        return $this->db_driver->getRow("ANALYZE TABLE `{DBPRAEFIX}" . $this->table_name . "`; ");
    }

    public function optimize () {
        //optimize table
        return $this->db_driver->listRows("OPTIMIZE TABLE `{DBPRAEFIX}" . $this->table_name . "`; ");
    }

    public function flush () {
        //flush table
        $this->db_driver->query("FLUSH TABLE `{DBPRAEFIX}" . $this->table_name . "`; ");
    }

    public function drop () {
        //drop table
        $this->db_driver->query("DROP TABLE `{DBPRAEFIX}" . $this->table_name . "`; ");
    }

    public function existsTable () : bool {
        print_r($this->db_driver->listRows("SHOW TABLES LIKE '{DBPRAEFIX}" . $this->table_name . "'; "));
        return count($this->db_driver->listRows("SHOW TABLES LIKE '{DBPRAEFIX}" . $this->table_name . "'; ")) > 0;
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

    public static function listIndexes (string $table_name, DBDriver $dbDriver) {
        return $dbDriver->listRows("SHOW INDEX FROM `{DBPRAEFIX}" . $table_name . "`; ");
    }

}