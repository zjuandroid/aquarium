<?php
return array(
	//'配置项' =>'配置值'
	'MODULE_ALLOW_LIST' =>    array('Home','Admin',),
	//我们用了入口版定 所以下面这行可以注释掉
	//'DEFAULT_MODULE'    =>    'Home',  // 默认模块	
	'SHOW_PAGE_TRACE'   =>  false,
//	'SHOW_PAGE_TRACE'   =>  true,
	'LOAD_EXT_CONFIG'   => 'db,wechat,oauth', 
	'URL_CASE_INSENSITIVE'  =>  true,  //url不区分大小写
	'URL_MODEL'   =>0,    //省略index.php，失败
	'URL_HTML_SUFFIX'  =>'html',
	//'DEFAULT_FILTER'        => 'htmlspecialchars',
	'SUPER_ADMIN_ID'=>1,  //超级管理员id 删除用户的时候用这个禁止删除
	'SHOW_ERROR_MSG'        =>  true, 
	//用户注册默认信息
	'DEFAULT_SCORE'=>100,
	'LOTTERY_NUM'=>3,  //每天最多的抽奖次数

	'SMS_API_KEY' => 'eb2323adc7bb33eab35a4d4f9843f425',
	'SMS_INTERFACE' => 'http://sms-api.luosimao.com/v1/send.json',
	'AVATAR_MAX_SIZE' => 1024*1024,
	'AVATAR_ROOT_PATH' => './Uploads/',
	'AVATAR_SAVE_PATH' => 'Avatar/',
	'AVATAR_FILE_EXT' => array('jpg', 'gif', 'png', 'jpeg'),
	'CITY_FILE_PATH' => './cities.json',

	'APP_KEY_ANDROID' => '575e335ae0f55a1141001196',
	'APP_MASTER_SECRET_ANDROID' => 'lbfmvyhrerw5iwjrnigakngyipuqrjdy',
	'APP_KEY_IOS' => '57614bca67e58e4799003aa0',
	'APP_MASTER_SECRET_IOS' => 'lev3ydtdsdprdbiuhektw3pimtae5joh',
	'MESSAGE_IMAGE_SAVE_PATH'  => 'Push/',
	'PAGE_COUNT' => 10,

	'LOG_RECORD' => true, // 开启日志记录
	'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR', // 只记录EMERG ALERT CRIT ERR 错误
	'LOG_TYPE'              =>  'File', // 日志记录类型 默认为文件方式


);