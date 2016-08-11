<?php

/**
 * This file was only created for test purposes!
 * You dont need this file in production!
 */

//define ROOT_PATH
define('DTU_ROOT_PATH', dirname(__FILE__) . "/");

//add autoloader
require("autoloader.php");

//create new database connection
$dbDriver = new MySQLDriver();
$dbDriver->connect(DTU_ROOT_PATH . "config/mysql.cfg.php");

?>