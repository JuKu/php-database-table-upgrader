<?php

/**
 * Created by PhpStorm.
 * User: Justin
 * Date: 07.07.2016
 * Time: 17:47
 */
interface DBDriver {

    //http://www.peterkropff.de/site/mysql/advanced_mysql.htm

    //http://www.peterkropff.de/site/php/pdo.htm

    public function connect ($config_path);

    public function update ($sql);

    public function execute ($sql);

    public function quote ($str) : string;

    public function query ($sql) : PDOStatement;

    public function listTables () : array;

    public function getRow ($sql, $params = array());

    public function listRows ($sql, $params = array()) : array;

    public function escape (string $str) : string;

    public function countQueries () : int;

    public function beginTransaction ();

    public function rollback ();

    public function commit ();

    public function prepare ($sql) : PDOStatement;

    public function listQueryHistory () : array;

    public function close ();

}