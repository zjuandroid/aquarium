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

        if($flag === false) {
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
        if(validateListStr($str) && strlen($str) > 2) {
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

    function getSocketInfo() {
        $condition['id'] = I('post.socketId');
        $dao = M('socket')->where($condition);
        $ret['name'] = $dao->getField('name');
        $ret['usage_month'] = $dao->getField('usage_month');
        $ret['usage_total'] = $dao->getField('usage_total');
        $ret['status'] = $dao->getField('status');

        $str = $dao->getField('port_list');
        $str = changeBracket($str);
//        $portList = M('socket_port')->field('id,name,status,dis_order,deviceId,deviceType,timer_list_name,timer_list')->where('id in '.$str)->select();
//        $portList = M('socket_port')->where('id in '.$str)->getField('id,name,status,dis_order,deviceId,deviceType,timer_list_name,timer_list');
        $portList = M('socket_port')->field('socket_id', true)->where('id in '.$str)->order('dis_order')->select();

        for($i = 0; $i < count($portList); $i++) {
            foreach($portList[$i] as $key=>$value) {
                if($key == 'timer_list') {
                    $port[$key] = json_decode($value);
                }
                else {
                    $port[$key] = $value;
                }
            }
            $ret['portList'][$i] = $port;
        }

        echo wrapResult('CM0000', $ret);
    }

    function getTimerList() {
        $str = I('post.timerList');
        $str = changeBracket($str);

        $timerList = M('timer')->field('name,socket_port', true)->where('id in '.$str)->select();
        for($i = 0; $i < count($timerList); $i++) {
            foreach($timerList[$i] as $key=>$value) {
                if($key == 'day_list') {
                    $timer[$key] = json_decode($value);
                }
                else {
                    $timer[$key] = $value;
                }
            }
            $ret['timerList'][$i] = $timer;
        }

        echo wrapResult('CM0000', $ret);
    }

    function getLightInfo1() {
        $lightId = I('post.lightId');

        $data = M('light')->field('tank_id', true)->where('id = '.$lightId)->select();

        if(!$data) {
            exit(wrapResult('FH0003'));
        }

        echo (wrapResult('CM0000', $data[0]));
    }

    function getLightInfo() {
        $lightId = I('post.lightId');

        $data = M('light')->field('tank_id', true)->where('id = '.$lightId)->select();

        if(!$data) {
            exit(wrapResult('FH0003'));
        }
        $data = $data[0];
        $str = $data['timer_list'];
        $data['timer_list'] = json_decode($str);

        echo (wrapResult('CM0000', $data));
    }

    function addDevice() {
        $deviceType = I('post.deviceType');
        $param['name'] = I('post.deviceName');
        $param['tank_id'] = I('post.fishTankId');

        $dao = M('fishtank')->where('id='.$param['tank_id']);
        //检查设备是否已超限
        if($deviceType == C('DEVICE_TYPE_LIGHT')) {
            $list = $dao->getField('light_list');
            if($list != null) {
                $cList =  json_decode($list);
                if(count($cList) >= 3) {
                    exit (wrapResult('FH0006'));
                }
            }
        }
        else if($deviceType == C('DEVICE_TYPE_THERMOMETER')) {
            $list = $dao->getField('thermometer_list');
            if($list != null) {
                $cList =  json_decode($list);
                if(count($cList) >= 3) {
                    exit (wrapResult('FH0006'));
                }
            }
        }
        else if($deviceType == C('DEVICE_TYPE_SOCKET')) {
            $socketId = $dao->getField('socket');
            if($socketId != null) {
                exit (wrapResult('FH0006'));
            }
        }

        //创建设备ID
        $id = 0;
        if($deviceType == C('DEVICE_TYPE_LIGHT')) {
            $id = M('light')->add($param);
        }
        else if($deviceType == C('DEVICE_TYPE_THERMOMETER')) {
            $id = M('thermometer')->add($param);
        }
        else if($deviceType == C('DEVICE_TYPE_SOCKET')) {
            $dao = M('socket_port');
            for($i = 0; $i < 6; $i++) {
                $portlist[$i] = (int)$dao->add('name=""');
                if(!$portlist[$i]) {
                    exit (wrapResult('FH0004'));
                }
            }
            $param['port_list'] = json_encode($portlist);
            $id = M('socket')->add($param);
        }
        else {
            exit (wrapResult('FH0005'));
        }

        if(!$id) {
            exit (wrapResult('FH0004'));
        }

        //更新fishTank表中对应索引
        $nId = (int) $id;


        $dao = M('fishtank')->where('id='.$param['tank_id']);
//        $kk = M('fishtank')->where('id=1')->select();
//        dump($dao->select());
//        dump($dao->select());
        if($deviceType == C('DEVICE_TYPE_LIGHT')) {
            $list = $dao->getField('light_list');
            if($list == null) {
                $cList[0] = $nId;
            }
            else {
                $cList =  json_decode($list);
                $cList[] = $nId;
            }

            //下面的dao取出来不止一条记录，疑似tp的bug
//            $flag = $dao->setField('light_list', json_encode($cList));
            $flag = M('fishtank')->where('id='.$param['tank_id'])->setField('light_list', json_encode($cList));
        }
        else if($deviceType == C('DEVICE_TYPE_THERMOMETER')) {
            $list = $dao->getField('thermometer_list');
            if($list == null) {
                $cList[0] = $nId;
            }
            else {
                $cList =  json_decode($list);
                $cList[] = $nId;
            }

            //下面的dao取出来不止一条记录，疑似tp的bug
//            $flag = $dao->setField('thermometer_list', json_encode($cList));
            $flag = M('fishtank')->where('id='.$param['tank_id'])->setField('thermometer_list', json_encode($cList));
        }
        else if($deviceType == C('DEVICE_TYPE_SOCKET')) {
            $flag = $dao->setField('socket', $nId);
        }

        if(!$flag) {
            exit (wrapResult('CM0002'));
        }

        //为保持与其他接口统一，同时避免整型超出取值范围，返回值为字符串
        $ret['deviceId'] = $id;

        echo (wrapResult('CM0000', $ret));
    }

    function setLightInfo() {
        $condition['id'] = I('post.deviceId');
        $data['r_value'] = I('post.rValue');
        $data['g_value'] = I('post.gValue');
        $data['b_value'] = I('post.bValue');
        $data['w_value'] = I('post.wValue');
        $data['x_value'] = I('post.xValue');

        $curValue = I('post.curValue');
        if($curValue) {
            $data['cur_value'] = $curValue;
        }
        $name = I('post.name');
        if($name) {
            $data['name'] = $name;
        }
        $disOrder = I('post.disOrder');
        if($disOrder) {
            $data['dis_order'] = $disOrder;
        }

        $timerList = I('post.timerList');
        if($timerList) {
            $data['timer_list'] = $timerList;
        }

        $timerListName = I('post.timerListName');
        if($timerListName) {
            $data['timer_list_name'] = $timerListName;
        }

        $dao = M('light')->where($condition);
        if(!$dao) {
            exit (wrapResult('FH0003'));
        }

        $flag = $dao->save($data);
        if($flag === false) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult('CM0000'));
    }

    function setTempInfo() {
        $condition['id'] = I('post.deviceId');
        $data['min_temp'] = I('post.minTemp');
        $data['max_temp'] = I('post.maxTemp');

        $curTemp = I('post.curTemp');
        if($curTemp) {
            $data['cur_temp'] = $curTemp;
        }
        $name = I('post.name');
        if($name) {
            $data['name'] = $name;
        }
        $disOrder = I('post.disOrder');
        if($disOrder) {
            $data['dis_order'] = $disOrder;
        }

        $dao = M('thermometer')->where($condition);
        if(!$dao) {
            exit (wrapResult('FH0003'));
        }

        $flag = $dao->save($data);
        if($flag === false) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult('CM0000'));
    }

    function setSocketInfo() {
        $condition['id'] = I('post.deviceId');
        $data['status'] = I('post.status');

        $name = I('post.name');
        if($name) {
            $data['name'] = $name;
        }
        $monthUsage = I('post.monthUsage');
        if($monthUsage) {
            $data['usage_month'] = $monthUsage;
        }
        $totalUsage = I('post.totalUsage');
        if($totalUsage) {
            $data['usage_total'] = $totalUsage;
        }

        $dao = M('socket')->where($condition);
        if(!$dao) {
            exit (wrapResult('FH0003'));
        }

        $flag = $dao->save($data);
        if($flag === false) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult('CM0000'));
    }

    function setSocketPortInfo() {
        $condition['id'] = I('post.socketPortId');
        $data['status'] = I('post.status');

        $name = I('post.portName');
        if($name) {
            $data['name'] = $name;
        }

        $order = I('post.disOrder');
        if($order) {
            $data['dis_order'] = $order;
        }

        $deviceId = I('post.connectedDeviceId');
        if($deviceId) {
            $data['deviceId'] = $deviceId;
        }

        $deviceType = I('post.connectedDeviceType');
        if($deviceType) {
            $data['deviceType'] = $deviceType;
        }

        $timerList = I('post.timerList');
        if($timerList) {
            $data['timer_list'] = $timerList;
        }

        $timerListName = I('post.timerListName');
        if($timerListName) {
            $data['timer_list_name'] = $timerListName;
        }

        $dao = M('socket_port')->where($condition);
        if(!$dao) {
            exit (wrapResult('FH0003'));
        }

        $flag = $dao->save($data);
        if($flag === false) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult('CM0000'));
    }

    function addTimer() {
        $ownerType = I('post.ownerType');
        $condition['id'] = I('post.ownerId');

        $data['name'] = I('post.timerName');
        $data['status'] = I('post.timerStatus');
        $data['start_time'] = I('post.startTime');
        $data['end_time'] = I('post.endTime');
        $data['day_list'] = I('post.dayList');

        $timerId = M('timer')->add($data);
        if(!$timerId) {
            exit (wrapResult('CM0002'));
        }

        if($ownerType == 'socketPort') {
            $dao = M('socket_port');
        }
        else if($ownerType == 'light') {
            $dao = M('light');
        }
        if(!$dao->where($condition)) {
            exit (wrapResult('FH0003'));
        }
        $str = $dao->where($condition)->getField('timer_list');
        if(!$str) {
            $str = '[]';
        }
        $list = json_decode($str);
        $list[] = (int)$timerId;

        $flag = $dao->where($condition)->setField('timer_list', json_encode($list));
        if(!$flag) {
            exit (wrapResult('CM0002'));
        }

        $ret['timerId'] = $timerId;
        echo (wrapResult('CM0000', $ret));
    }

    function setTimer()
    {
        $condition['id'] = I('post.timerId');

        $data['name'] = I('post.timerName');
        $data['status'] = I('post.timerStatus');
        $data['start_time'] = I('post.startTime');
        $data['end_time'] = I('post.endTime');
        $data['day_list'] = I('post.dayList');

        $dao = M('timer');
        $flag =  $dao->where($condition)->save($data);
        if($flag === false) {
            exit (wrapResult('CM0002'));
        }

        echo (wrapResult('CM0000'));
    }

    function getFishTankList() {
        $condition['userid'] = I('post.userid');

        $data = M('fishtank')->field('id,name,length,width,heigth')->where($condition)->select();
//        dump($data);
//        $i = 0;
//        foreach($data as $item) {
//            $fishTankArray[$i++] = (int)$item['id'];
//        }

        if(empty($data)) {
            $ret['fishTankArray'] = null;
        } else {
            $ret['fishTankArray'] = $data;
        }

        echo (wrapResult('CM0000', $ret));
    }


    function getFishTankInfo() {
        $condition['id'] = I('post.fishTankId');

        $data = M('fishtank')->field('name,length,width,heigth,opendate,fishkinds')->where($condition)->select();
        if(empty($data)) {
            exit (wrapResult('FH0002'));
        } else {
            $data = $data[0];
        }

        $str = $data['fishkinds'];
        if(validateListStr($str) && (strlen($str) > 2)) {
            $str = str_replace(array('[', ']'), array('(', ')'), $str);
            $result['fishKinds'] = M('fishkind')->field('id, name')->where('id in ' . $str)->select();
        }
        else {
            $result['fishKinds'] = null;
        }
        $result['name'] = $data['name'];
        $result['length'] = $data['length'];
        $result['width'] = $data['width'];
        $result['heigth'] = $data['heigth'];
        $result['openDate'] = $data['opendate'];

        echo wrapResult('CM0000', $result);
    }

}