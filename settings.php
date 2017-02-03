<?php
/**
 * dependencies:
 *      pear install --alldeps Mail_Queue
 */

// no-cache
header('Pragma: no-cache');
header('Cache-Control: private, no-cache, no-store, max-age=0, must-revalidate,'
        . ' proxy-revalidate');
header('Expires: Tue, 04 Sep 2012 05:32:29 GMT');

// autoload classes
function __autoload($classname) {
     include_once("class/$classname.php");
}

// check config file
Config::check();

// initialize DB
Db::initialize();