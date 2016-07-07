<?php
namespace Admin\Controller;
use Admin\Controller;
use Think\Upload;
/**
 * 链接管理
 */
class PushController extends BaseController
{
    /**
     * 链接列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key === ""){
            $model = M('message');
        }else{
            $where['title'] = array('like',"%$key%");
            $where['content'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = M('message')->where($where);
        } 
        
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $page = new \Extend\Page($count,C('PAGE_COUNT'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $page->show();// 分页显示输出
        $links = $model->limit($page->firstRow.','.$page->listRows)->where($where)->order('id DESC')->select();
        $this->assign('model', $links);
        $this->assign('page',$show);
        $this->display();     
    }

    function sendMessage($data) {
        $model = D("message");
        if (!$model->create()) {
            // 如果创建失败 表示验证没有通过 输出错误提示信息
            $this->error($model->getError());
            exit();
        } else {
            $store = $data['picture'];
            $data['picture'] = substr(C('AVATAR_ROOT_PATH'), 1).C('MESSAGE_IMAGE_SAVE_PATH').$data['picture'];
            $flag = sendAndroidMessage($data);
            if(!$flag) {
                $this->error("消息推送失败");
            }

            $flag = sendIOSBroadcast($data);
            if(!$flag) {
                $this->error("IOS消息推送失败");
            }
            $data['picture'] = $store;
            //type = 1, 表示新品推荐
            $data['type'] = 1;
            $data['create_time'] = time();
            if ($model->add($data)) {
//                $flag = M('member')->where('id=1')->setField('has_new_message', '1');
                $flag = M('member')->execute('update __TABLE__ set has_new_message=1');

                $this->success("消息推送成功", U('Push/index'));
            } else {
                $this->error("消息已经送出，但是没有记录在本地服务器中");
            }
        }
    }

    /**
     * 添加链接
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $data['title'] = I('post.title');
            $data['content'] = I('post.content');
            $data['url'] = I('post.url');

            $upload = new Upload();// 实例化上传类
            $upload->maxSize = C('AVATAR_MAX_SIZE') ;// 设置附件上传大小
            $upload->exts      =     C('AVATAR_FILE_EXT');// 设置附件上传类型
            $upload->rootPath  =     C('AVATAR_ROOT_PATH'); // 设置附件上传根目录
            $upload->savePath  =     C('MESSAGE_IMAGE_SAVE_PATH'); // 设置附件上传（子）目录
            $upload->autoSub = false;
            // 上传文件
            $info   =   $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            }else{// 上传成功
                $data['picture'] = $info['picture']['savename'];
//                $data['icon'] = $info['icon']['savename'];

//                dump($data);
                $this->sendMessage($data);
            }
        }
    }
    /**
     * 更新链接信息
     * @param  [type] $id [链接ID]
     * @return [type]     [description]
     */
    public function update($id)
    {
    		$id = intval($id);
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('links')->where("id= %d",$id)->find();
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("links");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
                if ($model->save()) {
                    $this->success("更新成功", U('links/index'));
                } else {
                    $this->error("更新失败");
                }        
            }
        }
    }
    /**
     * 删除链接
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
    		$id = intval($id);
        $model = M('links');
        $result = $model->delete($id);
        if($result){
            $this->success("链接删除成功", U('links/index'));
        }else{
            $this->error("链接删除失败");
        }
    }
}
