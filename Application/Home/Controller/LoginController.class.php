<?php

namespace Home\Controller;

use Think\Controller;

/**
 * 用户登陆模块
 */
class LoginController extends Controller{


    /*
     * 登录：
     * http://localhost/aquarium/index.php/login/login
     * POST参数：
     * username
     * password
     * 返回值：
     * 成功：{"code":"LG0000","message":"\u767b\u5f55\u6210\u529f","userid":"1","token":"tj9m5hiehxvz1pzohkezv8lpcevnmi2t"}
     * 失败：{"code":"LG0001","message":"\u7528\u6237\u540d\u6216\u5bc6\u7801\u9519\u8bef","userid":"","token":""}
     * */
    public function login() {
        if(!IS_POST){
            echo wrapResult('CM0001');
        }
        else {
            $data['username'] = I('post.phone');
            $data['password'] = I('post.password','','');

            $member = M('member')->where($data)->find();
            if($member){
                $status = M('member')->where($data)->getField('status');
                if($status == 0) {
                    exit (wrapResult('LG0005'));
                }

                $ret['token'] =  $this->createNonceStr(32);
                $ret['userid'] = $member['id'];
                S($member['id'], $ret['token']);
                echo wrapResult('LG0000', $ret);
            }else{
                echo wrapResult('LG0001');
            }
        }
    }

    /*
     * 用户注册/重置密码
     * type:  register/reset
     * http://localhost/aquarium/index.php/login/register
     * */
    function register() {
        if(!IS_POST) {
            echo $this->getJsonResult('CM0001', '', '');
        }
        else {
            $map['username'] = I('post.phone');
            $password = I('post.password');
            $smsCode = I('post.smsCode');
            $type = I('post.type');
            $cache = S($map['username']);

            if($cache != $smsCode) {
                exit ( wrapResult('LG0002'));
            }

            if($type == 'register') {
                $member = M('member')->where($map)->find();

                if($member) {
                    echo wrapResult('LG0003');
                    return;
                }
                $map['password'] = $password;
                $map['create_at'] = date('Y-m-d H:i:s');
                $map['update_at'] = date('Y-m-d H:i:s');
                $id = M('member')->add($map);
                if($id) {
                    $ret['token'] =  $this->createNonceStr(32);
                    $ret['userid'] = $id;
                    S($member['id'], $ret['token']);
                    echo(wrapResult('LG0000', $ret));
                }
                else {
                    echo(wrapResult('CM0002'));
                }
                return;
            }
            else if($type == 'reset'){
                $member = M('member')->where($map)->find();
                if(!$member) {
                    echo wrapResult('LG0004');
                    return;
                }
                $map['password'] = $password;
                $map['update_at'] = date('Y-m-d H:i:s');
                $flag = M('member')->where('username = '.$map['username'])->setField($map);
                if($flag !== false) {
                    echo(wrapResult('CM0000'));
                }
                else {
                    echo(wrapResult('CM0002'));
                }
            }
        }
    }

    function sendSMS($mobile, $vCode) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, C(SMS_INTERFACE));

        curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);

        curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-'.C(SMS_API_KEY));

        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $mobile,'message' => '验证码：'.$vCode.'，三分钟内有效。【吉印水族科技】'));

        $res = curl_exec( $ch );
        curl_close( $ch );
//$res  = curl_error( $ch );
//        dump ($res);

        return $res;
    }

    /*
     * 发送短信验证码
     * http://localhost/aquarium/index.php/login/sendSmsCode
     *
     * {"code":"SM0000","message":"\u942d?\u4fca\u9359\u6226?\u93b4\u612c\u59db"}
     * */
    function sendSmsCode() {
        if(!IS_POST) {
            exit ( wrapResult('CM0001') );
        }
        else {
            $mobile = I('post.phone');
            $vCode = rand(1000, 9999);
            S($mobile, $vCode, 180);
            $res = $this->sendSMS($mobile, $vCode);
//            return:   $res = '{"error":0,"msg":"ok"}';

            $obj = json_decode($res);

            if($obj->error == 0) {
                $code = 'SM0000';
            }
            else {
                $str = -($obj->error);
                $map['code'] = array('like', 'SM%'.$str);
                $code = M('errcode')->where($map)->getField('code');
            }

//            $map['code'] = array('eq', $result[code]);
//            $result[message] = M('errcode')->where($map)->getField('msg');

            echo(wrapResult($code));
        }
    }

    function getJsonResult($code, $userid, $token) {
        $response[code] = $code;
        $map['code'] = array('eq', $code);
        $response[message] = M('errcode')->where($map)->getField('msg');
        $response[userid] = $userid;
        $response[token] = $token;
        return json_encode($response);
    }

    /**
     *  作用：产生随机字符串，不长于32位
     */
    function createNonceStr( $length = 32 )
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    function getAllGeo() {
        $jsonStr = file_get_contents(C('CITY_FILE_PATH'));
        if(!$jsonStr) {
            echo (wrapResult('CM0005'));
        }
        else {
            echo (wrapResult('CM0000',json_decode($jsonStr) ));
        }
    }

}
