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

//create or upgrade test table
$table = new DBTable("test", $dbDriver);
$table->setEngine("InnoDB");
$table->setCharset("utf8");

//add int coloum
$table->addInt("id");

//add int coloum with length 10
$table->addInt("testint", 10);

//print CREATE statement for debugging
echo $table->generateCreateQuery();

?>