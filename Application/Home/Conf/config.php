<?php
return array(
    //主题静态文件路径
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__.'/Application/'.MODULE_NAME.'/View/' . '/Public/static'
    ),
    //CSRF
    'TOKEN_ON'      =>    true,  // 是否开启令牌验证 默认关闭
    'TOKEN_NAME'    =>    '__hash__',    // 令牌验证的表单隐藏字段名称，默认为__hash__
    'TOKEN_TYPE'    =>    'md5',  //令牌哈希验证规则 默认为MD5
    'TOKEN_RESET'   =>    true,  //令牌验证出错后是否重置令牌 默认为true
    //是否开启模板布局 根据个人习惯设置
    'LAYOUT_ON'=>false,

    'URL_MODEL'   =>0,    //省略index.php，失败
    'DAY_DOT_NUM' => 3,
    'MONTH_DOT_NUM' => 7,

    'DEVICE_TYPE_LIGHT' => 1,
    'DEVICE_TYPE_THERMOMETER' => 2,
    'DEVICE_TYPE_SOCKET' => 3,
    'DEVICE_TYPE_WATER_LEVEL' => 4,
    'DEVICE_TYPE_TELECONTROLLER' => 5


//    /* 自动运行配置 */
//    'CRON_CONFIG_ON' => true, // 是否开启自动运行
//    'CRON_CONFIG' => array(
//        '测试定时任务1' => array('Home/fish/crons', '3', '')//路径(格式同R)、间隔秒（0为一直运行）、指定一个开始时间
//    )

);