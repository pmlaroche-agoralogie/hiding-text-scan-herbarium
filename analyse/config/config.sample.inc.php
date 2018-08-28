<?php

$currentDir = dirname(__FILE__);

if (!defined('_CORE_DIR_')) {
    define('_CORE_DIR_', realpath($currentDir.'/..'));
}
define('_CLASS_DIR_', _CORE_DIR_.'/classes/');
define('_LOG_DIR_', _CORE_DIR_.'/logs/');

//DATABASE
define('DB_USER','');
define('DB_PASSWD','');
define('DB_NAME','');
define('DB_HOST','');
define('DB_PREFIXE','');

//BASE_URL
define('BASE_URL','');