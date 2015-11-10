<?php
/**
 * UDF管理器
 * User: goodspb
 * Date: 15/8/31
 * Time: 18:38
 */

namespace Home\Controller;

class UdfManageController extends BaseController{

    public function upload(){

        $module = I('module','');
        $type = I('type','');
        if(empty($type) || empty($module)){
            $this->error(L('empty_module_or_type'));
        }

        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('lua');// 设置附件上传类型
        $upload->rootPath  =     APP_REAL_PATH.'/upload/'; // 设置附件上传根目录
        $upload->saveName = '';
        $upload->autoSub = false;

        if(!is_dir($upload->rootPath)){
            mkdir($upload->rootPath);
        }

        if(!is_writable($upload->rootPath)){
            chmod($upload->rootPath,0777);
        }

        $info   =   $upload->uploadOne($_FILES['path']);
        if(!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        }else{//upload success
            $real_path =  $upload->rootPath.$info['savepath'].$info['savename'];
            $type = '.'.strtolower($type);
            if(stristr($module,$type) === false){
                $module .= $type;
            }
            $ret = $this->AM->createUDF($real_path , $module);
            if($ret===true){
                $this->success(L('msg_add_success'),U('Home/UdfManage/alist'));
                exit();
            }
            $this->error('Aerospike Error : '.$this->AM->getError(),U('Home/UdfManage/alist'));
        }
    }

    //新增
    function create(){
        $this->display();
    }

    function del(){
        $module = I('module');
        $ret = $this->AM->deleteUDF($module);
        if($ret===true){
            $this->success( L('msg_delete_success') ,U('Home/UdfManage/alist'));
            exit();
        }
        $this->error($this->AM->getError(),U('Home/UdfManage/alist'));
    }

    function alist(){
        $ret = $this->AM->getUDFlist();
        if($ret===false){
            exit($this->AM->getError());
        }
        //print_r($ret);
        $this->assign('list',$ret);
        $this->display();
    }



}
