# phpAerospikeAdmin

nosql数据库Aerospike的GUI管理工具

### 配置

1、确定已经安装Aerospike server 和 php Aerospike 扩展

2、打开 config.php 文件：

    //请按照自身aerospike服务器情况配置：

    aerospike' => array(
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

3、确保以下文件夹存在并由读写权限
~~~
/Application/Runtime
~~~
~~~
/upload/
~~~

4、enjoy it
