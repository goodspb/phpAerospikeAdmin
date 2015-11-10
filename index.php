<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//check the Aerospike extension
if(!class_exists('Aerospike')) die('The PHP Aerospike Extension is not found！Please check <a href="https://github.com/aerospike/aerospike-client-php">aerospike-client-php</a>');

if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
define('APP_DEBUG',True);
define('APP_PATH','./Application/');
define('APP_REAL_PATH',dirname(__FILE__));
require './ThinkPHP/ThinkPHP.php';
