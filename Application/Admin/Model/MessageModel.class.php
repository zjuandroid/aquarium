<?php
namespace Admin\Model;
use Think\Model;
class MessageModel extends Model{
    protected $_validate = array(
        array('title','require','请填写消息标题！'), //默认情况下用正则进行验证
        array('content','require','请填写消息内容！'), //默认情况下用正则进行验证
        array('picture','require','请上传广告图片！'), //默认情况下用正则进行验证
        array('url','require','请填写广告图片链接！'), //默认情况下用正则进行验证
    );

    protected $_auto = array(
        array('create_time','time',self::MODEL_INSERT,'function'), //新增时
    );


}