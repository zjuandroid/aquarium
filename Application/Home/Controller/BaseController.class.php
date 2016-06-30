<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 如果某个控制器必须用户登录才可以访问  
 * 请继承该控制器
 */
class BaseController extends Controller {
    public function _initialize1(){
        if(!IS_POST){
            exit(wrapResult('CM0001'));
        }

        $userid = I('post.userid');
        $token = I('post.token');

        if(!$userid || !$token) {
            exit(wrapResult('CM0003'));
        }

        if($token != S($userid)) {
            exit(wrapResult('CM0003'));
        }

        $status = M('member')->where('id='.$userid)->getField('status');
        if($status == 0) {
            exit (wrapResult('LG0005'));
        }
    }

}