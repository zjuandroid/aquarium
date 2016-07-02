<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;

/**
 * 如果某个控制器必须用户登录才可以访问  
 * 请继承该控制器
 */
class UserController extends BaseController {

    function logout() {
        if(!IS_POST) {
            echo wrapResult('CM0001');
            return;
        }

        $userid = I('post.userid');
        S($userid, NULL);
        echo (wrapResult('CM0000'));
    }

    function uploadAvatar() {
        $userid = I('post.userid');

        $upload = new Upload();// 实例化上传类
        $upload->maxSize = C('AVATAR_MAX_SIZE') ;// 设置附件上传大小
        $upload->exts      =     C('AVATAR_FILE_EXT');// 设置附件上传类型
        $upload->rootPath  =     C('AVATAR_ROOT_PATH'); // 设置附件上传根目录
        $upload->savePath  =     C('AVATAR_SAVE_PATH'); // 设置附件上传（子）目录
        $upload->autoSub = false;
        // 上传文件
        $info   =   $upload->upload();
        if(!$info) {// 上传错误提示错误信息
//            echo($upload->getError());
            //包装失败信息
            exit( wrapResult('CM0004'));
        }else{// 上传成功
            $map['avatar'] = $info['pic']['savename'];
            $map['update_at'] = time();
            $flag = M('member')->where('id = '.$userid)->setField($map);

            if($flag !== false) {
                $ret['avatar'] = substr(C('AVATAR_ROOT_PATH'),1).C('AVATAR_SAVE_PATH').$map['avatar'];
                echo(wrapResult('LG0000', $ret));
            }
            else {
                echo(wrapResult('CM0002'));
            }
        }
    }

    function changePhone() {
//        $phone = I('post.phone');
//        $smsCode = I('post.smsCode');
        $map['username'] = I('post.newPhone');
        $newSmsCode = I('post.newSmsCode');
        $userid = I('post.userid');
        $cache = S($map['username']);

        if($cache != $newSmsCode) {
            exit ( wrapResult('LG0002'));
        }
        $map['update_at'] = time();

        $flag = M('member')->where('id='.$userid)->save($map);

        if($flag !== false) {
            echo(wrapResult('CM0000'));
        }
        else {
            echo(wrapResult('CM0002'));
        }
    }

    function getAllGeo() {
        $jsonStr = file_get_contents(C('CITY_FILE_PATH'));
        if(!$jsonStr) {
            echo (wrapResult('CM0005'));
        }
        else {
            echo (wrapResult('CM0000',$jsonStr ));
        }
    }

    function setUserInfo() {
        $condition['id'] = I('post.userid');
        $map['nickname'] = I('post.nickName');
        $map['good_at'] = I('post.fishType');
        $map['gender'] = I('post.gender');
        $map['feed_year'] = I('post.feedYears');

        $map['area_address'] = I('post.areaDesc');
        $map['district_address'] = I('post.district');
        $map['update_at'] = time();

        $flag = M('member')->where($condition)->save($map);

        if($flag !== false) {
            echo(wrapResult('CM0000'));
        }
        else {
            echo(wrapResult('CM0002'));
        }
    }

    function getUserInfo1() {
        $condition['id'] = I('post.userid');
        $dao = M('member');
        $result['nickName'] = $dao->where($condition)->getField('nickname');
        $path = $dao->where($condition)->getField('avatar');
        if($path) {
            $result['avatar'] = substr(C('AVATAR_ROOT_PATH'), 1).C('AVATAR_SAVE_PATH').$path;
        }
        else {
            $result['avatar'] = '';
        }
        $result['fishType'] = $dao->where($condition)->getField('good_at');
        $result['gender'] = $dao->where($condition)->getField('gender');
        $result['feedYears'] = $dao->where($condition)->getField('feed_year');
        $result['areaDesc'] = $dao->where($condition)->getField('area_address');
        $result['district'] = $dao->where($condition)->getField('district_address');
        $result['phone'] = $dao->where($condition)->getField('username');

//        $ret['code'] = 'CM0000';
//        $ret['message'] = M('errcode')->where($ret)->getField('msg');
//        $ret['data'] = $result;

        echo (wrapResult('CM0000', $result));
    }

    function getUserInfo2() {
        $condition['id'] = I('post.userid');
        $dao = M('member');
        $result['nickName'] = $dao->where($condition)->getField('nickname');
        $path = $dao->where($condition)->getField('avatar');

        if($path) {
            $result['avatar'] = substr(C('AVATAR_ROOT_PATH'), 1).C('AVATAR_SAVE_PATH').$path;
        }
        else {
            $result['avatar'] = '';
        }

        $str = $dao->where($condition)->getField('good_at');
        $str = str_replace(array('[', ']'), array('(', ')'), $str);
        $result['fishType'] = M('fishkind')->field('id, name')->where('id in '.$str)->select();
        $result['gender'] = $dao->where($condition)->getField('gender');
        $result['feedYears'] = $dao->where($condition)->getField('feed_year');
        $result['areaDesc'] = $dao->where($condition)->getField('area_address');
        $result['district'] = $dao->where($condition)->getField('district_address');
        $result['phone'] = $dao->where($condition)->getField('username');

        echo (wrapResult('CM0000', $result));
    }

    function getUserInfo() {
        $condition['id'] = I('post.userid');

        $data = M('member')->field('nickname,avatar,good_at,gender,feed_year,area_address,district_address,username')->where($condition)->select();
        if(!$data) {
            exit (wrapResult('CM0006'));
        }
        $data = $data[0];

        $result['nickName'] = $data['nickname'];
        $path = $data['avatar'];
        $result['avatar'] = $path ? substr(C('AVATAR_ROOT_PATH'), 1).C('AVATAR_SAVE_PATH').$path : '';

        $str = $data['good_at'];

        if(validateListStr($str) && (strlen($str) > 2)) {
            $str = str_replace(array('[', ']'), array('(', ')'), $str);
            $result['fishType'] = M('fishkind')->field('id, name')->where('id in ' . $str)->select();
        }
        else {
            $result['fishType'] = null;
        }
        $result['gender'] = $data['gender'];
        $result['feedYears'] = $data['feed_year'];
        $result['areaDesc'] = $data['area_address'];
        $result['district'] = $data['district_address'];
        $result['phone'] = $data['username'];

        echo (wrapResult('CM0000', $result));
    }

    function feedback() {
        $map['userid'] = I('post.userid');
        $map['content'] = I('post.content');
        $map['answerd'] = 0;
//        $map['feedback_time'] = date('Y-m-d H:i:s');
        $map['feedback_time'] = time();

        $flag = M('feedback')->add($map);

        if($flag) {
            echo(wrapResult('CM0000'));
        }
        else {
            echo(wrapResult('CM0002'));
        }

    }

    function custFeedback() {
        $condition['userid'] = I('post.userid');
        $condition['answerd'] = 1;
        $condition['isread'] = 0;

//        $code = 'CM0000';
 //        $flag = M('feedback')->where($condition)->find();
//        dump($condition);
//        dump($flag);
//        if($flag != null) {
//            $result['data'] = M('feedback')->where($condition)->getField('answer,answer_time');
//        }
//        else {
//            $result['data'] = null;
//        }
//        dump($result);
//        $result['data'] = M('feedback')->field('answer_time','answer')->where($condition)->select();

//        $data = M('feedback')->where($condition)->getField('id,answer_time,answer', ':');
//        $data = M('feedback')->where($condition)->getField('id,answer_time,answer');
        $data = M('feedback')->field('id,answer_time,answer')->where($condition)->select();

        $update['isread'] = 1;
//        dump(date('Y-m-d H:i:s'));
        if($data) {
            // 有反馈
//            $flag = M('feedback')->where($condition)->setField('isread', '1');
            $flag = M('feedback')->where($condition)->save($update);

            if($flag === false) {
                exit (wrapResult('CM0002'));
            }
            else {
                echo wrapResult('CM0000', $data);
            }
        }
        else {
            echo wrapResult('CM0000');
        }

    }

    function getMessages()
    {
        $condition['userid'] = I('post.userid');
        $condition['fetched'] = 0;

        $result['code'] = 'CM0000';
        $result['message'] = M('errcode')->where($result)->getField('msg');
        $result['data'] = M('message')->where($condition)->select();
//        dump($result['data']);
//        dump(M('message')->where($condition)->getField('userid,author,icon'));
//        dump(M('message')->field('id,userid,author,icon')->where($condition)->select());

        $data = M('message')->field('id,author,icon,title,creat_time,url,type,content')->where($condition)->select();

        $update['fetched'] = 1;
        if($data) {
            // 有消息
            $flag = M('message')->where($condition)->save($update);

            if($flag === false) {
                exit (wrapResult('CM0002'));
            }
            else {
                echo wrapResult('CM0000', $data);
            }
        }
        else {
            echo wrapResult('CM0000');
        }
    }

}