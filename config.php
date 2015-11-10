<?php
/**
 * User: goodspb
 * Date: 15/10/12
 * Time: 17:59
 */

return array(

    'aerospike' => array(
        'namespace' => 'test',
        'connection' => array(
            "hosts" => array(
                array(
                    "addr" => "127.0.0.1",      //服务器地址
                    "port" => 3000              //服务器端口
                )
            )
        ),
        'connect_timeout' => 500,   //链接超时时间
        'read_timeout'	=> 500,		//读超时时间
        'write_timeout' => 500,		//取超时时间
    )

);
