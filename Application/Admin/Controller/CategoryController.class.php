<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 分类管理
 */
class CategoryController extends BaseController
{
    /**
     * 分类列表
     * @return [type] [description]
     */
    public function index1($key="")
    {
        if($key === ""){
            $model = M('feedback');
            $where['answered'] = 0;
        }else{
            //            $where['title'] = array('like',"%$key%");
//            $where['name'] = array('like',"%$key%");
//            $where['_logic'] = 'or';
//            $model = M('category')->where($where);
        }

        $count = $model->where($where)->count();
        $pagecount = 15;
//        $page = new \Think\Page($count , $pagecount);
        $page = new \Extend\Page($count , $pagecount);
        //$page->parameter = $row; //此处的row是数组，为了传递查询条件
//        $page->setConfig('first','首页');
//        $page->setConfig('prev','上一页');
//        $page->setConfig('next','下一页');
//        $page->setConfig('last','尾页');
//        $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% 第 '.I('p',1).' 页/共 %TOTAL_PAGE% 页 ( '.$pagecount.' 条/页 共 %TOTAL_ROW% 条)');
        $show = $page->show();
        //$list = $db->where($where)->order('id desc')->limit($page->firstRow.','.$page->listRows)->select();
        //$this->assign('list',$list);
        $this->assign('page',$show);

        $feedback = $model->limit($page->firstRow.','.$page->listRows)->where($where)->order('id ASC')->select();
        $dao = M('member');
        for($i = 0; $i < count($feedback); $i++) {
            $feedback[$i]['username'] = $dao->where('id='.$feedback[$i]['userid'])->getField('username');
        }
        $this->assign('model',getSortedCategory($feedback));
        $this->display();   
    }

    public function index($key="")
    {
        if($key === ""){
            $model = M('feedback');
            $where['answered'] = 0;
        }else{
            $where['username'] = array('like',"%$key%");
            $where['content'] = array('like',"%$key%");
            $where['_logic'] = 'or';
//            $model = M()->field('t1.username, t2.id, t2.userid, t2.content')->table('member as t1')->join('feedback as t2 on t1.id=t2.userid');
            $member = M('member');
            $model = $member->join('feedback ON member.id = feedback.userid');
        }

        $count = $model->where($where)->count();
        $pagecount = 10;
        $page = new \Extend\Page($count , $pagecount);
        $show = $page->show();
        $this->assign('page',$show);

        //join的结果只能用一次，可能是thinkphp的bug
        if($key !== ''){
            $model = $member->join('feedback ON member.id = feedback.userid');
        }
        $feedback = $model->limit($page->firstRow.','.$page->listRows)->where($where)->order('feedback.id ASC')->select();
        $dao = M('member');
        for($i = 0; $i < count($feedback); $i++) {
            $feedback[$i]['username'] = $dao->where('id='.$feedback[$i]['userid'])->getField('username');
        }
        $this->assign('model',getSortedCategory($feedback));
        $this->display();
    }

    function answer() {
        if (!IS_POST) {
            $id = I('get.id');
//            $data = M('feedback')->where('id=' . $id)->select();
            $data = M('member')->join('feedback ON member.id = feedback.userid')->where('feedback.id=' . $id)->select();
            if($data) {
                $data = $data[0];
            }
            $this->assign('model', $data);
            $this->display();
        }
        if(IS_POST) {
            $condition['id'] = I('post.id');
            $data['answered'] = 1;
//            $data['answer_time'] = date('Y-m-d H:i:s');
            $data['answer_time'] = time();
            $data['answer'] = I('post.answer');

            $flag = M('feedback')->where($condition)->save($data);

            if ($flag) {
                $this->success("回复成功", U('category/index'));
            } else {
                $this->error("回复失败");
            }
        }
    }

    /**
     * 删除分类
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function delete($id)
    {
        $model = M('feedback');
        //查询属于这个分类的文章
        $result = $model->delete($id);
        if($result){
            $this->success("用户意见删除成功", U('category/index'));
        }else{
            $this->error("用户意见删除失败");
        }
    }
}
