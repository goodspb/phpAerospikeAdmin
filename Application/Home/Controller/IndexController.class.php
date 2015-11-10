<?php

namespace Home\Controller;

class IndexController extends BaseController {


    public function index(){
        $namespace = I('namespace',$this->now_namespace);
        $info = $this->AM->getNamespace($namespace);

        $this->assign('info',$info);
        $this->display();
    }

    public function getSets(){
        $namespace = I('namespace',$this->now_namespace);
        $namespace = $this->check_namespace($namespace);
        $this->set_default_namespace($namespace);

        $list = $this->AM->getSets($namespace);
        $this->assign('list',$list);
        $this->display();
    }

    public function getIndexs(){
        $namespace = I('namespace',$this->now_namespace);
        $namespace = $this->check_namespace($namespace);
        $this->set_default_namespace($namespace);

        $indexs = $this->AM->getIndexs($namespace);
        $this->assign('indexs',$indexs);
        $this->display();
    }

    //管理index索引
    public function ManageIndex(){


        if(IS_POST){
            $namespace = I('namespace',$this->get_default_namespace());
            $indexneme = I('indexneme');
            $bin = I('bin');
            $bintype = I('bintype');
            $setname = I('set','');

            $do = I('do','create');
            if($do == 'create'){

                if(empty($indexneme) || empty($bin) || empty($bintype)){
                    $this->error(L('error_empty'));
                }
                $ret = $this->AM->createIndex($namespace , $indexneme , $bin , $bintype , $setname);
                if($ret===false){
                    $this->error(L('msg_add_fail'));
                }
                $this->success(L('msg_add_success'),U('Home/Index/getSets',array('namespace'=>$namespace)));
            }
            //删除INDEX
            elseif($do == 'delete'){
                if(empty($indexneme)){
                    $this->error( L('error_empty') );
                }
                $ret = $this->AM->deleteIndex($namespace , $indexneme ,$setname);
                if($ret===false){
                    $this->error( L('msg_delete_fail') );
                }
                $this->success( L('msg_delete_success') ,U('Home/Index/getSets',array('namespace'=>$namespace)));
            }
            exit();
        }

        $this->display();
    }


//未实现，不知道OK不OK
//    public function deleteSet(){
//
//        $namespace = $this->get_default_namespace();
//        $set = I('set','');
//        if(empty($set)){
//            $this->error( L('error_param') );
//        }
//        print_r($this->AM->deleteSet($namespace,$set));
//        $this->success( L('msg_delete_success') );
//    }

    public function getRecords(){

        //获取默认namespace
        $namespace = $this->get_default_namespace();

        $set = I('set','');
        $count = I('count',0);

        if(empty($set) || $count == 0){
            $this->error( L('error_param') );
        }
        $list = $this->AM->ASscan($namespace,$set,1,$count);
        $column = array_keys(current($list));
//        print_r($column);
//        print_r($list);

        $this->assign('set',$set);
        $this->assign('count',$count);
        $this->assign('column',$column);
        $this->assign('list',$list);
        $this->display();

    }

    public function getOneRecord(){

        $namespace = $this->get_default_namespace();
        $set = I('set','');
        $key = I('key','');
        if(empty($set) || empty($key)){
            $this->error( L('error_param') );
        }

        $ret = $this->AM->ASget($namespace,$set,$key);
        if($ret===false){
            $this->error( L('msg_interface_error').'：'.$this->AM->getError());
        }elseif($ret===-1){
            $this->error( L('no_record') );
        }

//        print_r($ret['bins']);

        $this->assign('set',$set);
        $this->assign('rekey',$key);
        $this->assign('record',$ret['bins']);
        $this->assign('empty', L('empty'));
        $this->assign('list',$ret);
        $this->display();

    }

    public function deleteRecord(){

        $namespace = $this->get_default_namespace();
        $set = I('set','');
        $key = I('key','');
        if(empty($set) || empty($key)){
            $this->error( L('error_param') );
        }

        $ret = $this->AM->ASremoveRecord($namespace,$set,$key);
        $backurl = U('Home/Index/getRecords',array('set'=>$set,'key'=>$key,'count'=>I('count')));
        if($ret===-1){
            $this->error( L('no_record') , $backurl);
        }elseif($ret===false){
            $this->error( L('msg_interface_error').'：'.$this->AM->getError(),$backurl);
        }else{
            $this->success( L('msg_delete_success') ,$backurl);
        }
    }


    function manageUDF(){


        $this->display();
    }

}
