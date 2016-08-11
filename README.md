# php-database-table-upgrader
An database upgrader for upgrade mysql table structures automatically

Version: 0.0.1 alpha

## Requirements
  - PHP 7.0.8+
  - MySQL 5.7+
  
## Configuration
If you want to use the build-in MySQLDriver, you have to copy config/mysql.examplecfg.php to mysql.cfg.php and change values.
  
## Supported Data Types
  - INT
  - VARCHAR
  - TEXT
  - BIT
  - BINARY
  
  - more are Work in Progress
  
## Example

Generate CREATE TABLE query:

```php
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

//print CREATE statement for debugging
echo $table->generateCreateQuery();
```

prints following query:
```sql
CREATE TABLE `{DBPRAEFIX}test` IF NOT EXISTS (
`id` INT,
`testint` INT(10) NOT NULL AUTO_INCREMENT,
`test_text` VARCHAR(255) NOT NULL DEFAULT 'default value',
`text` TEXT
) TYPE=InnoDB DEFAULT CHARSET=utf8;
```