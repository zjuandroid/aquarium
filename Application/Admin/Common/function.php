<?php
use Think\Log;

function sendAndroidMessage($data) {
    vendor('Umeng.android.AndroidBroadcast');

    try {
        $brocast = new \AndroidBroadcast();
        $brocast->setAppMasterSecret(C('APP_MASTER_SECRET_ANDROID'));
        $brocast->setPredefinedKeyValue("appkey",           C('APP_KEY_ANDROID'));
//            $brocast->setAppMasterSecret($appMasterSecret);
//            $brocast->setPredefinedKeyValue("appkey",           $appKey);
        $brocast->setPredefinedKeyValue("timestamp",        strval(time()));
        $brocast->setPredefinedKeyValue("ticker",           "吉印水族应用有新消息，请注意查看。");
        $brocast->setPredefinedKeyValue("title",            $data['title']);
        $brocast->setPredefinedKeyValue("text",             $data['content']);
        $brocast->setPredefinedKeyValue("after_open",       "go_app");
        // Set 'production_mode' to 'false' if it's a test device.
        // For how to register a test device, please see the developer doc.
        $brocast->setPredefinedKeyValue("production_mode", "true");
        // [optional]Set extra fields
//        $brocast->setExtraField("icon", $data['icon']);
        $brocast->setExtraField("pic", $data['pic']);
        $brocast->setExtraField("url", $data['url']);
//        print("Sending broadcast notification, please wait...\r\n");

        $brocast->send();
    } catch (Exception $e) {
//        print("Caught exception: " . $e->getMessage());
        Log::record(date("Y-m-d H:i:s").' android消息推送异常 '.$e->getMessage());
        return false;
    }

    return true;
}

function sendIOSBroadcast($data) {
    vendor('Umeng.ios.IOSBroadcast');
    try {
        $brocast = new \IOSBroadcast();
        $brocast->setAppMasterSecret(C('APP_MASTER_SECRET_IOS'));
        $brocast->setPredefinedKeyValue("appkey",            C('APP_KEY_IOS'));
        $brocast->setPredefinedKeyValue("timestamp",       strval(time()));

        $brocast->setPredefinedKeyValue("alert", "吉印水族应用有新消息");
        $brocast->setPredefinedKeyValue("badge", 0);
        $brocast->setPredefinedKeyValue("sound", "chime");
        // Set 'production_mode' to 'true' if your app is under production mode
        $brocast->setPredefinedKeyValue("production_mode", "false");
        // Set customized fields
        $brocast->setCustomizedField("title", $data['title']);
        $brocast->setCustomizedField("text", $data['content']);
        $brocast->setCustomizedField("pic", $data['pic']);
        $brocast->setCustomizedField("url", $data['url']);
//        print("Sending broadcast notification, please wait...\r\n");
        $brocast->send();
//        print("Sent SUCCESS\r\n");
    } catch (Exception $e) {
//        print("Caught exception: " . $e->getMessage());
        Log::record(date("Y-m-d H:i:s").' ios消息推送异常 '.$e->getMessage());
        return false;
    }

    return true;
}

function getFishNameStr($str) {
    $str = changeBracket($str);
    $fishList = M('fishkind')->field('name')->where('id in '.$str)->select();
    $str = '';
    foreach($fishList as $fish) {
        $str .= $fish['name'].',';
    }
    return substr($str, 0, -1);
}