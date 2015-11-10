<?php
/**
 * AerospikeManager
 * User: goodspb
 * Date: 15/8/27
 * Time: 15:50
 */

namespace Home\Lib;

class AerospikeManager{

    public $aerospike_config;
    /* @var \Aerospike $aerospike  */
    public $aerospike = null;
    public static $errorno = null;
    public static $error = null;

    function __construct(){

        $config = $this->aerospike_config = C('aerospike');
        $aerospike =  $this->aerospike = new \Aerospike($config['connection'],true,array(
            \Aerospike::OPT_CONNECT_TIMEOUT => $config['connect_timeout'],
            \Aerospike::OPT_READ_TIMEOUT => $config['read_timeout'],
            \Aerospike::OPT_WRITE_TIMEOUT => $config['write_timeout'],
        ));

        if (!$aerospike->isConnected()) {
            exit('链接错误，请检查config配置或aerospike是否启动');
        }

        //定义常量
        define('AS_OK',\Aerospike::OK);
        define('AS_RECORD_NOT_FOUND', \Aerospike::ERR_RECORD_NOT_FOUND);
        define('AS_RECORD_EXISTS', \Aerospike::ERR_RECORD_EXISTS);

    }

    function getNamespace($namespace){

        $command = 'namespace/'.$namespace;
        if($info = $this->ASinfo($command)){
            $_temp = explode(';',$info);
            $result = array();
            foreach($_temp as $key=>$val){
                $_temp2 = explode('=',$val);
                if(isset($_temp2[0])){
                    $result[$_temp2[0]] = $_temp2[1];
                }
            }
            return $result;
        }
        return false;
    }


    function getNamespaces(){
        if($namespaces = $this->ASinfo('namespaces')){
            return explode(';',$namespaces);
        }
        return false;
    }

    function getSets($namespace){
        $command = 'sets/' . $namespace;
        if ($info = $this->ASinfo($command)) {
            $list = array_filter(explode(';',$info));
            foreach ($list as $k => $l){
                $list[$k] = explode(':',$l);
                $_temp = array();
                foreach($list[$k] as $kk=>$vv){
                    $_temp2 = explode('=',$vv);
                    $_temp[$_temp2[0]] = $_temp2[1];
                }
                $list[$k] = $_temp;
            }
            return $list;
        }
        return false;
    }

    /**
     * 删除set
     * @param $namespace_name
     * @param $set_name
     * @return bool
     */
    function deleteSet($namespace_name,$set_name){
        $command = "set-config:context=namespace;id={$namespace_name};set={$set_name};set-delete=true;";
        return $this->ASinfo($command);
    }

    function getIndexs($namespace){
        $command = 'sindex/' . $namespace;
        if ($info = $this->ASinfo($command)) {
            $list = array_filter(explode(';',$info));
            foreach ($list as $k => $l){
                $list[$k] = explode(':',$l);
                $_temp = array();
                foreach($list[$k] as $kk=>$vv){
                    $_temp2 = explode('=',$vv);
                    $_temp[$_temp2[0]] = $_temp2[1];
                }
                $list[$k] = $_temp;
            }
            return $list;
        }
        return false;
    }

    /**
     * 添加index
     * @param $namespace_name
     * @param $bin_name
     * @param $bin_type
     * @param $set_name
     * @return array|bool
     */
    function createIndex($namespace_name,$indexname,$bin_name,$bin_type='string',$set_name = null){
        $bin_types = array('numeric','string');
        $bin_type = in_array($bin_type,$bin_types) ? $bin_type : 'string';
        $command = 'sindex-create:ns=' . $namespace_name . ';'.($set_name!=null?'set='.$set_name:'').';indexname='.$indexname.';indexdata='.$bin_name.','.$bin_type;
        if ($info = $this->ASinfo($command)) {
            return $info;
        }
        return false;
    }

    /**
     * 删除index
     * @param $namespace_name
     * @param $indexname
     * @param null $set_name
     * @return bool
     */
    function deleteIndex($namespace_name,$indexname,$set_name = null){
        $command = 'sindex-delete:ns=' . $namespace_name . ';'.($set_name!=null?'set='.$set_name:'').';indexname='.$indexname.';';
        if ($info = $this->ASinfo($command)) {
            return $info;
        }
        return false;
    }

    /**
     * 向服务期注册lua脚本
     * @param $path
     * @param $module
     * @return bool
     */
    function createUDF($path , $module){
        $status = $this->aerospike->register($path, $module);
        if ($status == AS_OK) {
            return true;
        }
        return $this->setError();
    }

    /**
     * 删除服务器中的lua脚本
     * @param $module
     * @return bool
     */
    function deleteUDF($module){
        $status = $this->aerospike->deregister($module);
        if ($status == AS_OK) {
            return true;
        }
        return $this->setError();
    }

    /**
     * 获取UDF列表
     * @return bool
     */
    function getUDFlist(){
        $status = $this->aerospike->listRegistered($modules);
        if ($status == AS_OK) {
            return $modules;
        }
        return $this->setError();
    }

    /**
     * 获取单一个UDF脚本
     * @param $module
     * @return bool|int
     */
    function getUDFcode($module){
        $status = $this->aerospike->getRegistered($module, $code);
        if ($status == AS_OK) {
            return $code;
        }elseif ($status == \Aerospike::ERR_LUA_FILE_NOT_FOUND) {
            return -1;
        } else {
            return $this->setError();
        }
    }

    //返回最后一次执行的错误
    static function getError($sep = true){
        return $sep ? self::$errorno.'：'.self::$error : array(self::$errorno,self::$error);
    }

    /**
     * 设置错误
     */
    function setError(){
        self::$errorno = $this->aerospike->errorno();
        self::$error = $this->aerospike->error();
        return false;
    }

    /**
     * AS操作符命令
     * @param $command
     * @return bool
     */
    function ASinfo($command){

        if($this->aerospike->info($command, $info) == AS_OK){
            return trim(substr($info, strlen($command)));
        }
        return false;

    }

    //扫描set里面所有项
    function ASscan($ns,$set,$page = 1 ,$pagesize = 20){

        $result = array();
        $counter = 0;
        $ret = $this->aerospike->scan($ns,$set,function ($record) use (&$result,&$counter,&$page,&$pagesize){
//            print_r($record);
            if($counter>=($page-1)*$pagesize && $counter<=$page*$pagesize){
                $result[$record['key']['key']] = $record['bins'];
            }
            $counter++;
        });

        if ($ret !== AS_OK) {
            return $this->setError();
        }

        return $result;
    }


    function ASquery(){



    }

    /**
     * 获取一条记录
     * @param $namespace
     * @param $set
     * @param $key
     * @return bool|int 返回数据
     */
    function ASget($namespace,$set,$key){

        $keys = $this->ASinitKey($namespace,$set,$key);
        $status = $this->aerospike->get($keys, $record);

        if ($status == AS_OK) {
            return $record;
        } elseif ($status == AS_RECORD_NOT_FOUND) {
            return -1;
        } else {
            return $this->setError();
        }

    }

    /**
     * 返回一个查找record的key
     * @param $namespace
     * @param $set
     * @param $key
     */
    function ASinitKey($namespace,$set,$key){
        $key = is_numeric($key) ? (int)$key : $key;
        return $this->aerospike->initKey($namespace,$set,$key);
    }

    /**
     * 根据key删除整个record
     * @param $namespace
     * @param $set
     * @param $key
     * @param $bins 如果有该数组，就删除record里面的bin
     */
    function ASremoveRecord($namespace,$set,$key,$bins = array()){
        $key = $this->ASinitKey($namespace,$set,$key);
        if(empty($bins) || !is_array($bins)){
            $status = $this->aerospike->remove($key);
        }else{
            $status = $this->aerospike->removeBin($key,$bins);
        }
        if ($status == AS_OK) {
            return true;
        } elseif ($status == AS_RECORD_NOT_FOUND) {
            return -1;
        } else {
            return $this->setError();
        }
    }

    /**
     * 写入record , 如果bin已经存在，则更新
     * @param $namespace
     * @param $set
     * @param string or int $key
     * @param array $bins 需要写入的bin
     */
    function ASputRecord($namespace,$set,$key,$bins,$options = array()){

        $key = $this->ASinitKey($namespace,$set,$key);
        if(empty($options) || !is_array($options)){
            $status = $this->aerospike->put($key,$bins);
        }else{
            $status = $this->aerospike->put($key,$bins,$options);
        }
        if ($status == AS_OK) {
            return true;
        }
        return $this->setError();
    }

}
