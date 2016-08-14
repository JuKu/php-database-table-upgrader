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

//add int coloum with length 10, NOT NULL and AUTO_INCREMENT
$table->addInt("testint", 10, true, true);

//add varchar column
$table->addVarchar("test_text", 255, true, "default value");

//add text column
$table->addText("text");

//add decimal with length of 5 and digits after komma of 2
$table->addDecimal("decimal1", 5, 2);

//add DOUBLE with length of 5, 2 and NOT NULL
$table->addDouble("double1", 5, 2, true);

//add ENUM with 3 values and NOT NULL
$table->addEnum("options", array("option1", "option2", "option3"), true);

//add ENUM with 3 values, NOT NULL and default value
$table->addEnum("new_options", array("option1", "option2", "option3"), true, "option1");

//add TIMESTAMP with NOT NULL and default timestamp
$table->addTimestamp("date", true, "0000-00-00 00:00:00");

//add TIMESTAMP which is set to CURRENT_TIMESTAMP on every update
$table->addTimestamp("date1", true, "0000-00-00 00:00:00", true);

//add primary key
//$table->addPrimaryKey(array("id", "testint", array('column' => "test_text", 'length' => 50)));

//if you only want to add 1 column to primary key, you can use this instead:
$table->addPrimaryKey("testint");

//add index
$table->addIndex("test_text");

//add UNIQUE key
$table->addUnique("decimal1");

//add FULLTEXT index
//$table->addFulltext("text");

//add multi column index
$table->addIndex(array("options", "new_options"), "ix_options");

//print CREATE statement for debugging
echo $table->generateCreateQuery();

//upgrade table structure or create table, if table not exists
$table->upgrade();

//list columns from database
var_dump($table->listColumnsFromDatabase());

var_dump(DBTable::getTableStructure("test", $dbDriver));

?>