<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 链接管理
 */
class LinksController extends BaseController
{
    /**
     * 链接列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key === ""){
            $model = M('links');  
        }else{
            $where['title'] = array('like',"%$key%");
            $where['url'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = M('links')->where($where); 
        } 
        
        $count  = $model->where($where)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $links = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('id DESC')->select();
        $this->assign('model', $links);
        $this->assign('page',$show);
        $this->display();     
    }

    function test() {
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
            $brocast->setPredefinedKeyValue("title",            "中文的title");
            $brocast->setPredefinedKeyValue("text",             "Android broadcast text");
            $brocast->setPredefinedKeyValue("after_open",       "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $brocast->setPredefinedKeyValue("production_mode", "true");
            // [optional]Set extra fields
            $brocast->setExtraField("test", "helloworld");
            print("Sending broadcast notification, please wait...\r\n");
            $brocast->send();
            print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
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
            $model = D("links");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->success("链接添加成功", U('links/index'));
                } else {
                    $this->error("链接添加失败");
                }
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
