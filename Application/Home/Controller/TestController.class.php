<?php

namespace Home\Controller;

use Think\Controller;
use Org\Util\Date;

class TestController extends Controller{

    /**
     *  作用：产生随机字符串，不长于32位
     */
    function createNoncestr( $length = 32 )
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        echo $str;
    }

    public function login(){
        $url = 'http://localhost/aquarium/index.php/login/login';
//        $post_data['appid']       = '10';
//        $post_data['appkey']      = 'cmbohpffXVR03nIpkkQXaAA1Vf5nO4nQ';
//        $post_data['username'] = 'chwang';
//        $post_data['password']    = '123456';

        $post_data['phone'] = '18121380371';
        $post_data['password']    = '111111';
//        $post_data['email']    = 'zsjs123@126.com';


        $res = request_post($url, $post_data);
        print_r($res);
    }

    public function sendCode(){
        $url = 'http://localhost/aquarium/index.php/login/sendSmsCode';

//        $url = 'http://120.27.216.57/login/sendSmsCode';

        $post_data['phone'] = '18121380371';
//        $post_data['phone'] = '15260223203';
//        $post_data['phone'] = '';


        $res = request_post($url, $post_data);
        print_r($res);
    }

    public function register(){
        $url = 'http://localhost/aquarium/index.php/login/register';


        $post_data['phone'] = '18121380371';
        $post_data['password']    = '21232f297a57a5a743894a0e4a801fc3';
        $post_data['smsCode']    = '1217';
        $post_data['type']    = 'register';

        $res = request_post($url, $post_data);
        print_r($res);
    }



    function logout() {
        $url = 'http://localhost/aquarium/index.php/user/logout';

        $post_data['userid'] = '1';
        $post_data['token']    = 'z4lqdcqzkufk0n0i3bqrkk80gbwcd5un';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function upload() {
        $url = 'http://localhost/aquarium/index.php/user/uploadAvatar';

        $pic = 'pic';
        $userid = '1';
        $token = '';

        upload_file($url,'aa2', './','jpg', $pic, $userid, $token);
    }

    function changePhone() {
        $url = 'http://localhost/aquarium/index.php/user/changePhone';
        $post_data['userid'] = '1';
        $post_data['token']    = 'z4lqdcqzkufk0n0i3bqrkk80gbwcd5un';
        $post_data['newPhone'] = '18121380371';
        $post_data['newSmsCode'] = '8893';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function setUserInfo()
    {
        $url = 'http://localhost/aquarium/index.php/user/setUserInfo';
        $post_data['userid'] = '1';
        $post_data['token']    = 'z4lqdcqzkufk0n0i3bqrkk80gbwcd5un';
        $post_data['nickName'] = 'haha';
        $post_data['fishType'] = '[1,2,3]';
        $post_data['gender'] = '男';
        $post_data['feedYears'] = 8;
        $post_data['areaDesc'] = '上海市浦东新区';
        $post_data['district'] = '益江路299弄';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getUserInfo() {
        $url = 'http://localhost/aquarium/index.php/user/getUserInfo';
        $post_data['userid'] = '1';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function feedback() {
        $url = 'http://localhost/aquarium/index.php/user/feedback';
        $post_data['userid'] = '1';
        $post_data['content'] = '早上好！';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function custFeedback() {
        $url = 'http://localhost/aquarium/index.php/user/custFeedback';
        $post_data['userid'] = '1';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getMessages() {
        $url = 'http://localhost/aquarium/index.php/user/getMessages';
        $post_data['userid'] = '1';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getAllKindFish() {
        $url = 'http://localhost/aquarium/index.php/fish/getAllKindFish';
        $post_data['userid'] = '1';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function addFishKind() {
        $url = 'http://localhost/aquarium/index.php/fish/addFishKind';
        $post_data['userid'] = '1';
        $post_data['fishName'] = '他的鱼';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function addFishTank() {
        $url = 'http://localhost/aquarium/index.php/fish/addFishTank';
        $post_data['userid'] = '1';
        $post_data['name'] = 'wc';
        $post_data['length'] = 3.3;
        $post_data['width'] = 2.2;
        $post_data['heigth'] = 1.1;
        $post_data['openDate'] = '20160908';
        $kinds = json_encode(array(1,3,4)) ;
        dump($kinds);
        $post_data['fishKinds'] = $kinds;

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function updateFishTank() {
        $url = 'http://localhost/aquarium/index.php/fish/updateFishTank';

        $post_data['userid'] = 1;
        $post_data['fishTankId'] = 1;

        $post_data['name'] = 'kkk';
        $post_data['length'] = 13.3;
        $post_data['width'] = 12.2;
        $post_data['heigth'] = 11.1;
        $post_data['openDate'] = '2016090811';
        $post_data['fishKinds'] = '[8,9]';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function deleteFishTank() {
        $url = 'http://localhost/aquarium/index.php/fish/deleteFishTank';

        $post_data['userid'] = 1;
        $post_data['fishTankId'] = 5;

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getMyFishTank() {
        $url = 'http://localhost/aquarium/index.php/fish/getMyFishTank';

        $post_data['userid'] = 1;
        $post_data['fishTankId'] = 1;

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function generateTempHis1() {
        $userid = 1;
        $tank_id = 1;
        $thermometer_id = 1;

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
        for($i=$day; $i <= ($startDate->lastDayOfMonth()->getDay());$i++) {
            for($j=1; $j <= 24; $j++ ) {
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

        if($month != $nowMonth) {
            for($i=1; $i <= $nowDay;$i++) {
                for($j=1; $j <= 24; $j++ ) {
                    $condition['thermometer_id'] = $thermometer_id;
                    $condition['year'] = $nowYear;
                    $condition['month'] = $nowMonth;
                    $condition['day'] = $i;
                    $condition['hour'] = $j;
                    $condition['temperature'] = rand(0, 45);
                    $condition['tank_id'] = $tank_id;

                    $id = $dao->add($condition);
                }
            }
        }

    }

    function generateTempHis() {
        $url = 'http://localhost/aquarium/index.php/fish/generateTempHis';

        $post_data['userid'] = 1;
        $post_data['fishTankId'] = 1;
        $post_data['thermometerId'] = 1;

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getTankTemp() {
        $url = 'http://localhost/aquarium/index.php/fish/getTankTemp';

        $post_data['userid'] = 1;
        $post_data['fishTankId'] = 1;
        $post_data['thermometerId'] = 1;
//        $post_data['type'] = 'hour';
//        $post_data['type'] = 'day';
        $post_data['type'] = 'month';

        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getSocketInfo() {
        $url = 'http://localhost/aquarium/index.php/fish/getSocketInfo';

        $post_data['socketId'] = 1;
        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getTimerList() {
        $url = 'http://localhost/aquarium/index.php/fish/getTimerList';

        $post_data['timerList'] = '[1,2]';
        $post_data['userid'] = '1';
        $res = request_post($url, $post_data);
        print_r($res);
    }

    function getLightInfo() {
        $url = 'http://localhost/aquarium/index.php/fish/getLightInfo';

        $post_data['lightId'] = 1;
        $post_data['userid'] = '1';
        $res = request_post($url, $post_data);
        print_r($res);
    }



    function test() {
//        $a = '[1,2,4]';
//        $obj = json_decode($a);
//        dump($obj);
        echo date("Y-m-d H:i:s",1466354044);     # 格式化时间戳
    }

}
