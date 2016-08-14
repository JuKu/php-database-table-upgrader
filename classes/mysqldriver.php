<?php

/**
 * MySQL Driver Class
 *
 * This file belongs originally to https://github.com/JuKu/php-scaling-core-framework project and is licensed under MIT License.
 */
class MySQLDriver implements DBDriver {

    protected $host = "localhost";
    protected $port = 3306;
    protected $username = "";
    protected $password = "";
    protected $praefix = "";
    protected $database = "";
    protected $options = array();

    protected $queries = 0;
    protected static $query_history = array();

    protected $conn = null;

    protected $prepared_cache = array();

    public function connect ($config_path) {
        if (file_exists($config_path)) {
            require($config_path);
        } else {
            throw new ConfigurationException("Couldnt found database configuration file " . $config_path . ".");
        }

        //get mysql connection data from configuration
        $this->host = $mysql_settings['host'];
        $this->port = $mysql_settings['port'];
        $this->username = $mysql_settings['username'];
        $this->password = $mysql_settings['password'];
        $this->praefix = $mysql_settings['praefix'];
        $this->database = $mysql_settings['database'];
        $this->options = $mysql_settings['options'];

        try {
            //create new database instance
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->database . "", $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            echo "Couldnt connect to database!";
            echo $e->getTraceAsString();

            throw $e;
        }
    }

    public function update ($sql) {
        $this->execute($sql);
    }

    public function close () {
        $this->conn = null;
    }

    public function execute ($sql, $params = array()) {
        //dont allow SELECT statements
        if (strstr($sql, "SELECT")) {
            throw new IllegalArgumentException("method DBDriver::execute() isnt for select statements, its only for write statements, use getRow() or listRows() instead.");
        }

        //add query to history
        self::$query_history[] = array('query' => $sql, 'params' => $params);

        //increment query counter
        $this->queries++;

        //prepare mysql statement
        $stmt = $this->prepare($sql);

        //bind parameters
        foreach ($params as $key=>$value) {
            if (is_array($value)) {
                $stmt->bindValue(":" . $key, $value['value'], $value['type']);
            } else {
                $stmt->bindValue(":" . $key, $value, PDO::PARAM_STR);
            }
        }

        //execute query
        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "An Error oncurred. Please contact administrator.<br /><br /><small>If you are the administrator: You can enable DEBUG MODE in LIB_PATH/store/settings/settings.php .</small>";

            if (!defined("DEBUG_MODE") || !DEBUG_MODE) {
                exit;
            }

            echo "<br /><br /><b>Query</b>: " . $sql . ", parameters: ";
            var_dump($params);

            echo "<br /><br /><b>PDO Statement: </b>";
            print_r($stmt);

            exit;
        } catch (Exception $e) {
            echo "An Error oncurred. Please contact administrator.<br /><br /><small>If you are the administrator: You can enable DEBUG MODE in LIB_PATH/store/settings/settings.php .</small>";

            if (!defined("DEBUG_MODE") || !DEBUG_MODE) {
                exit;
            }

            echo "<br /><br /><b>Query</b>: " . $sql . ", parameters: ";
            var_dump($params);

            echo "<br /><br /><b>PDO Statement: </b>";
            print_r($stmt);

            exit;
        }
    }

    public function listAllDrivers () {
        return $this->conn->getAvailableDrivers();
    }

    public function quote ($str) : string {
        return $this->conn->quote($str);
    }

    private function getQuery ($sql) {
        /**
         * check, if sql query contains comments
         *
         * because many SQL Injections uses sql comments, we dont allow mysql comments here
         */
        if (strstr($sql, "--")) {
            throw new SecurityException("SQL comments arent allowed here! Please remove sql comments from query!");
        }

        $sql = str_replace("{DBPRAEFIX}", $this->praefix, $sql);
        $sql = str_replace("{praefix}", $this->praefix, $sql);
        return str_replace("{PRAEFIX}", $this->praefix, $sql);
    }

    public function query($sql) : PDOStatement {
        //add query to history
        self::$query_history[] = array('query' => $sql);

        //increment query counter
        $this->queries++;

        return $this->conn->query($this->getQuery($sql));
    }

    public function listTables() : array {
        $rows = $this->query("SHOW TABLES;")->fetchAll();

        return $rows;
    }

    public function getRow($sql, $params = array()) {
        //get prepared statement
        $stmt = $this->prepare($sql);

        foreach ($params as $key=>$value) {
            if (is_array($value)) {
                $stmt->bindValue(":" . $key, $value['value'], $value['type']);
            } else {
                $stmt->bindValue(":" . $key, $value, PDO::PARAM_STR);
            }
        }

        //add query to history
        self::$query_history[] = array('query' => $sql, 'params' => $params);

        //increment query counter
        $this->queries++;

        //execute query
        $stmt->execute();

        //fetch row
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listRows($sql, $params = array()) : array {
        //get prepared statement
        $stmt = $this->prepare($sql);

        //add query to history
        self::$query_history[] = array('query' => $sql, 'params' => $params);

        //increment query counter
        $this->queries++;

        foreach ($params as $key=>$value) {
            if (is_array($value)) {
                $stmt->bindValue(":" . $key, $value['value'], $value['type']);
            } else {
                $stmt->bindValue(":" . $key, $value, PDO::PARAM_STR);
            }
        }

        //execute query
        $stmt->execute();

        //fetch rows
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function escape(string $str) : string {
        return $this->quote($str);
    }

    public function countQueries() : int {
        return $this->queries;
    }

    public function beginTransaction () {
        $this->conn->beginTransaction();
    }

    public function rollback() {
        $this->conn->rollback();
    }

    public function commit() {
        $this->conn->commit();
    }

    public function prepare($sql) : PDOStatement {
        $sql = $this->getQuery($sql);

        if (isset($this->prepared_cache[md5($sql)])) {
            return $this->prepared_cache[md5($sql)];
        } else {
            $stmt = $this->conn->prepare($sql);

            //put prepared statement into cache
            $this->prepared_cache[md5($sql)] = $stmt;
            return $stmt;
        }
    }

    public function listQueryHistory() : array {
        return self::$query_history;
    }

    public function getDatabaseName() : string {
        return $this->database;
    }
}