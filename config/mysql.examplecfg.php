<?php

/**
 * MySQL Configuration file
 */

//for security reasons, mysql.cfg.php can only included once
if (defined('PSCF_MYSQL_SETTINGS_INCLUDED')) {
    echo "mysql configuration already included. Abort now!";
    exit;
}

$mysql_settings = array(
    /**
     * MySQL Host
     *
     * For example "localhost" or the ip of your mysql server
     */
    'host' => "localhost",

    /**
     * MySQL Port
     *
     * by default 3306
     */
    'port' => 3306,

    /**
     * MySQL database user
     *
     * If you have an choice: Dont use "root" !
     */
    'username' => "root",

    /**
     * MySQL database password
     */
    'password' => "<Insert your password here>",

    /**
     * MySQL database name
     */
    'database' => "pscf",

    /**
     * MySQL table praefix (praefix before table name, for example "prefix_<Table Name>")
     *
     * Also "" is allowed
     */
    'praefix' => "pscf_",

    'options' => array(
        /**
         * use connection pooling, dont create an new connection on every request, use connection caching
         *
         * @link http://php.net/manual/de/pdo.connections.php
         */
        PDO::ATTR_PERSISTENT => true
    )
);

define('PSCF_MYSQL_SETTINGS_INCLUDED', true);

?>