<?php

$home_config = array(
	//'config key'=>'config value'
    'DEFAULT_AJAX_RETURN' => 'JSON' ,
);

$aerospike_config = require_once THINK_PATH.'../config.php';

return array_merge($home_config,$aerospike_config);
