<?php

namespace Home\Controller;
use Think\Controller;
use Home\Lib\AerospikeManager;

class BaseController extends Controller{

    public $AM;
    public $namespaces;
    public $now_namespace = null;

    function __construct(){
        parent::__construct();

        $this->AM  = $AM = new AerospikeManager();

        $this->namespaces = $this->AM->getNamespaces();;
        if($getNamespace = I('namespace','')){
            $this->now_namespace = $this->set_default_namespace($getNamespace);
        }
        if($this->now_namespace == null) {
            $this->now_namespace = $this->get_default_namespace();
            if(!$this->now_namespace){
                $this->now_namespace = $this->namespaces[0];
            }
        }
        $this->assign('namespace',$this->namespaces);
    }

    function check_namespace($namespace = ''){
        return $this->now_namespace = in_array($namespace , $this->namespaces) ? $namespace :  $this->namespaces[0];
    }

    function set_default_namespace($namespace){
        return session('now_namespace',$namespace);
    }

    function get_default_namespace(){
        return session('now_namespace');
    }

    function display($templateFile='',$charset='',$contentType='',$content='',$prefix=''){
        //写出每次的now_namespace
        $this->assign('now_namespace',$this->now_namespace);
        parent::display($templateFile,$charset,$contentType,$content,$prefix);
    }

    function ajaxResponse($status = 1 , $msg = '' , $data = array()){
        $ret = array(
            'status'    => $status,
            'msg'       => $msg,
            'data'      => $data
        );
        $this->ajaxReturn($ret);
    }


}
