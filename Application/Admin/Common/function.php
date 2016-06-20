<?php
function sendMessage($data) {
    vendor('Umeng.android.AndroidBroadcast');

    $appKey = '575e335ae0f55a1141001196';
    $appMasterSecret = 'lbfmvyhrerw5iwjrnigakngyipuqrjdy';

    try {
        $brocast = new \AndroidBroadcast();
        $brocast->setAppMasterSecret(C('APP_MASTER_SECRET_ANDROID'));
        $brocast->setPredefinedKeyValue("appkey",           C('APP_KEY_ANDROID'));
//            $brocast->setAppMasterSecret($appMasterSecret);
//            $brocast->setPredefinedKeyValue("appkey",           $appKey);
        $brocast->setPredefinedKeyValue("timestamp",        strval(time()));
        $brocast->setPredefinedKeyValue("ticker",           "Android broadcast ticker");
        $brocast->setPredefinedKeyValue("title",            $data['title']);
        $brocast->setPredefinedKeyValue("text",             $data['content']);
        $brocast->setPredefinedKeyValue("after_open",       "go_app");
        // Set 'production_mode' to 'false' if it's a test device.
        // For how to register a test device, please see the developer doc.
        $brocast->setPredefinedKeyValue("production_mode", "true");
        // [optional]Set extra fields
        $brocast->setExtraField("icon", $data['icon']);
        $brocast->setExtraField("pic", $data['pic']);
        $brocast->setExtraField("url", $data['url']);
        print("Sending broadcast notification, please wait...\r\n");
        $brocast->send();
        print("Sent SUCCESS\r\n");
    } catch (Exception $e) {
        print("Caught exception: " . $e->getMessage());
    }
}