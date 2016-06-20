<?php
namespace Admin\Controller;
use Think\Controller;

class BaseController extends Controller {
    public function _initialize1(){
        $sid = session('adminId');
        //判断用户是否登陆
        if(!isset($sid ) ) {
            redirect(U('Login/index'));
        }

    }

}