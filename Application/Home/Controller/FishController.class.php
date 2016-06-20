<?php
namespace Home\Controller;
use Think\Controller;
use Think\Upload;
use Org\Util\Date;

/**
 * 如果某个控制器必须用户登录才可以访问  
 * 请继承该控制器
 */
class FishController extends BaseController {

    function getAllKindFish() {
        $userid = I('post.userid');
        $data = M('fishkind')->field('id,name')->where('type = 0 or userid='.$userid)->select();
        echo (wrapResult('CM0000', $data));
    }

    function addFishKind() {
        $userid = I('post.userid');
        $data['name'] = I('post.fishName');
        $data['type'] = 1;
        $data['userid'] = $userid;

        $flag = M('fishkind')->where($data)->find();

        if($flag) {
            exit (wrapResult('FH0001'));
        }

        $id = M('fishkind')->add($data);

        if(!$id) {
            exit (wrapResult('CM0002'));
        }

        $ret['fishKindId'] = $id;

        echo (wrapResult('FH0000', $ret));

    }

    function addFishTank() {
        $data['userid'] = I('post.userid');
        $data['name'] = I('post.name');
        $data['length'] = I('post.length');
        $data['width'] = I('post.width');
        $data['heigth'] = I('post.heigth');
        $data['opendate'] = I('post.openDate');
        $data['fishkinds'] = I('post.fishKinds');

        $id = M('fishtank')->add($data);

        if(!$id) {
            exit (wrapResult('CM0002'));
        }

        $ret['fishTankId'] = $id;

        echo (wrapResult("FH0000", $ret));
    }

    function updateFishTank() {
        $condition['userid'] = I('post.userid');
        $condition['id'] = I('post.fishTankId');
        $data['name'] = I('post.name');
        $data['length'] = I('post.length');
        $data['width'] = I('post.width');
        $data['heigth'] = I('post.heigth');
        $data['opendate'] = I('post.openDate');
        $data['fishkinds'] = I('post.fishKinds');

        $flag = M('fishtank')->where($condition)->find();
//        $flag = M('fishtank')->where('userid=1 & id=1')->find();

        if(!$flag) {
            exit (wrapResult('FH0002'));
        }

        $flag = M('fishtank')->where($condition)->save($data);

        if(!$flag) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult("CM0000"));

    }

    function deleteFishTank() {
        $condition['userid'] = I('post.userid');
        $condition['id'] = I('post.fishTankId');

        $flag = M('fishtank')->where($condition)->find();
        if(!$flag) {
            exit (wrapResult('FH0002'));
        }

        $flag = M('fishtank')->where($condition)->delete();

        if(!$flag) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult("CM0000"));
    }

    function getMyFishTank() {
        $condition['id'] = I('post.fishTankId');
        $condition['userid'] = I('post.userid');

        $dao = M('fishtank')->where($condition);
        if(!$dao) {
            exit (wrapResult('FH0002'));
        }

        $data['status'] = $dao->getField('tank_status');
//        $data['preSetTempreture'] = $dao->getField('pre_set_temp');
        $str = $dao->getField('thermometer_list');
        if($str) {
            $str = str_replace(array('[',']'), array('(', ')'), $str);
//            $data['thermometerList'] = M('thermometer')->where('id in '.$str)->getField('id,name,cur_temp');
            $data['thermometerList'] = M('thermometer')->field('id,name,cur_temp,dis_order')->where('id in '.$str)->order('dis_order')->select();
        }
        else {
            $data['thermometerList'] = null;
        }

        $str = $dao->getField('socket');
        if($str) {
            $data['socket'] = M('socket')->field('id,name,usage_month,usage_total')->where('id='.$str)->select()[0];
        }
        else {
            $data['socket'] = null;
        }

        $str = $dao->getField('light_list');
        if($str) {
            $str = str_replace(array('[',']'), array('(', ')'), $str);
//            $data['lightList'] = M('light')->field('id,cur_value,dis_order')->where('id in '.$str)->select();
            $data['lightList'] = M('light')->field('id,cur_value,dis_order')->where('id in '.$str)->order('dis_order')->select();
        }
        else {
            $data['light'] =  null;
        }

        echo(wrapResult('CM0000', $data));

    }

    function generateTempHis()
    {
        $userid = I('post.userid');
        $tank_id = I('post.fishTankId');
        $thermometer_id = I('post.thermometerId');

        $date = new Date();
        $startDate = $date->dateAdd(-28);


        $year = $startDate->getYear();
        $month = $startDate->getMonth();
        $day = $startDate->getDay();
        $hour = $startDate->getHour();

        $nowYear = $date->getYear();
        $nowMonth = $date->getMonth();
        $nowDay = $date->getDay();
        $nowHour = $date->getHour();

        $dao = M('thermometer_his');

//        dump($startDate->lastDayOfMonth()->getDay());
        for ($i = $day; $i <= ($startDate->lastDayOfMonth()->getDay()); $i++) {
            for ($j = 0; $j <= 23; $j++) {
                $condition['thermometer_id'] = $thermometer_id;
                $condition['year'] = $year;
                $condition['month'] = $month;
                $condition['day'] = $i;
                $condition['hour'] = $j;
                $condition['temperature'] = rand(0, 45);
                $condition['tank_id'] = $tank_id;

                $id = $dao->add($condition);
            }
        }

        if ($month != $nowMonth) {
            for ($i = 1; $i <= $nowDay; $i++) {
                for ($j = 0; $j <= 23; $j++) {
                    $condition['thermometer_id'] = $thermometer_id;
                    $condition['year'] = $nowYear;
                    $condition['month'] = $nowMonth;
                    $condition['day'] = $i;
                    $condition['hour'] = $j;
                    $condition['temperature'] = rand(0, 40);
                    $condition['tank_id'] = $tank_id;

                    $id = $dao->add($condition);
                }
            }
        }

        echo (wrapResult("CM0000"));
    }

    function getTankTemp1() {
        $thermometerId = I('post.thermometerId');
        $tankId = I('post.fishTankId');
        $type = I('post.type');

        $dao = M('fishtank')->where('id='.$tankId);
        if(!$dao) {
            exit (wrapResult('FH0002'));
        }
        $data['auto'] = $dao->getField('temp_mode_auto');
        $data['preSetTemp'] = $dao->getField('pre_set_temp');

        $dao = M('thermometer')->where('id='.$thermometerId.' and tank_id='.$tankId);
        if(!$dao) {
            exit (wrapResult('FH0003'));
        }
        $data['name'] = $dao->getField('name');
        $data['maxTemp'] = $dao->getField('max_temp');
        $data['minTemp'] = $dao->getField('min_temp');
        $data['curTemp'] = $dao->getField('cur_temp');

        $nowDate = new Date();
        $nowYear = $nowDate->getYear();
        $nowMonth = $nowDate->getMonth();
        $nowDay = $nowDate->getDay();
        $nowHour = $nowDate->getHour();

        $condition['thermometer_id'] = $thermometerId;
        $condition['tank_id'] = $tankId;

        $dao = M('thermometer_his');
        if($type == 'hour') {
            $startDate = $nowDate->dateAdd(-24, 'h');
            $condition['year'] = $startDate->getYear();
            $condition['month'] = $startDate->getMonth();
            $condition['day'] = $startDate->getDay();
            $condition['hour'] = $startDate->getHour();

            $num = 0;
            for($i = $condition['hour']; $i <= 24; $i++ ) {
                $condition['hour'] = $i;
                $xy['x'] = $i;
                $xy['y'] = $dao->where($condition)->getField('temperature');
                $data['xy'][$num++] = $xy;
            }
            if($nowDay != $condition['day']) {
                for($i = 1; $i <= $nowHour; $i++) {
                    $condition['hour'] = $i;
                    $xy['x'] = $i;
                    $xy['y'] = $dao->where($condition)->getField('temperature');
                    $data['xy'][$num++] = $xy;
                }
            }
        }
        else if($type == 'day') {
            $startDate = $nowDate->dateAdd(-7, 'd');
            $condition['year'] = $startDate->getYear();
            $condition['month'] = $startDate->getMonth();
            $condition['day'] = $startDate->getDay();
            $condition['hour'] = $startDate->getHour();

            if($nowMonth != $condition['month']) {
                for($i = $condition['day']; $i <= $startDate->lastDayOfMonth()->getDay(); $i++) {
                    $step = 24/C('DAY_DOT_NUM');
                }
            }


        }

        echo (wrapResult('CM0000', $data));

    }


    function getTankTemp() {
        $thermometerId = I('post.thermometerId');
        $tankId = I('post.fishTankId');
        $type = I('post.type');

        $dao = M('fishtank')->where('id='.$tankId);
        if(!$dao) {
            exit (wrapResult('FH0002'));
        }
        $data['auto'] = $dao->getField('temp_mode_auto');
        $data['preSetTemp'] = $dao->getField('pre_set_temp');

        $dao = M('thermometer')->where('id='.$thermometerId.' and tank_id='.$tankId);
        if(!$dao) {
            exit (wrapResult('FH0003'));
        }
        $data['name'] = $dao->getField('name');
        $data['maxTemp'] = $dao->getField('max_temp');
        $data['minTemp'] = $dao->getField('min_temp');
        $data['curTemp'] = $dao->getField('cur_temp');

        $nowDate = new Date();
        $nowYear = $nowDate->getYear();
        $nowMonth = $nowDate->getMonth();
        $nowDay = $nowDate->getDay();
        $nowHour = $nowDate->getHour();

        $condition['thermometer_id'] = $thermometerId;
        $condition['tank_id'] = $tankId;

        $dao = M('thermometer_his');
        if($type == 'hour') {
            for($i = 24; $i >= 0; $i--) {
                $tempDate = $nowDate->dateAdd(-$i, 'h');
                $condition['year'] = $tempDate->getYear();
                $condition['month'] = $tempDate->getMonth();
                $condition['day'] = $tempDate->getDay();
                $condition['hour'] = $tempDate->getHour();

                $xy['x'] = $tempDate->getDate();
                $xy['y'] = $dao->where($condition)->getField('temperature');
                $data['xy'][24-$i] = $xy;
            }
        }
        else if($type == 'day') {
            for($i = 24*7; $i >= 0; $i--) {
                $tempDate = $nowDate->dateAdd(-$i, 'h');
                $condition['year'] = $tempDate->getYear();
                $condition['month'] = $tempDate->getMonth();
                $condition['day'] = $tempDate->getDay();
                $condition['hour'] = $tempDate->getHour();

                $xy['x'] = $tempDate->getDate();
                $xy['y'] = $dao->where($condition)->getField('temperature');
                $data['xy'][24*7-$i] = $xy;
            }
        }
        else if($type == 'month') {
                for($i = 24*7*4; $i >= 0; $i--) {
                    $tempDate = $nowDate->dateAdd(-$i, 'h');
                    $condition['year'] = $tempDate->getYear();
                    $condition['month'] = $tempDate->getMonth();
                    $condition['day'] = $tempDate->getDay();
                    $condition['hour'] = $tempDate->getHour();

                    $xy['x'] = $tempDate->getDate();
                    $xy['y'] = $dao->where($condition)->getField('temperature');
                    $data['xy'][24*7*4-$i] = $xy;
                }
        }

        echo (wrapResult('CM0000', $data));

    }

}