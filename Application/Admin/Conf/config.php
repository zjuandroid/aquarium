<?php
return array(
	//'配置项'=>'配置值'
    //主题静态文件路径
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__.'/Application/'.MODULE_NAME.'/View/' . '/Public/static',   ),
    'TMPL_ACTION_SUCCESS'=>'Public:dispatch_jump',
    'TMPL_ACTION_ERROR'=>'Public:dispatch_jump',
);