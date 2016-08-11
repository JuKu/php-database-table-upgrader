<?php

/**
 * Autoloader to include classes automatically, if they are required
 */

//define ROOT_PATH if neccessary
if (!defined("DTU_ROOT_PATH")) {
    define('DTU_ROOT_PATH', dirname(__FILE__) . "/");
}

//function to auto load classes
function loadClass ($class_name) {
    //convert to lower case
    $class_name = strtolower($class_name);

    if (file_exists(DTU_ROOT_PATH . "classes/" . $class_name . ".php")) {
        require(DTU_ROOT_PATH . "classes/" . $class_name . ".php");
    }
}

//register autoloader
spl_autoload_register("loadClass");

?>