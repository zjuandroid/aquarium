<?php
namespace Admin\Controller;
use Admin\Controller;
/**
 * 分类管理
 */
class FeedbackController extends BaseController
{
    /**
     * 分类列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key === ""){
            $model = M('feedback');
//            $where['answered'] = 1;
        }else{
            $where['username'] = array('like',"%$key%");
            $where['content'] = array('like',"%$key%");
            $where['_logic'] = 'or';
//            $where = 'answered = 0 and (username like "%'.$key.'%" or content like "%'.$key.'%")';
//            dump($where);
//            $model = M()->field('t1.username, t2.id, t2.userid, t2.content')->table('member as t1')->join('feedback as t2 on t1.id=t2.userid');
            $member = M('member');
            $model = $member->join('feedback ON member.id = feedback.userid');
        }

        $count = $model->where($where)->count();
        $page = new \Extend\Page($count, C('PAGE_COUNT'));
        $show = $page->show();
        $this->assign('page',$show);

        //join的结果只能用一次，可能是thinkphp的bug
        if($key !== ''){
            $model = $member->join('feedback ON member.id = feedback.userid');
        }
        $feedback = $model->limit($page->firstRow.','.$page->listRows)->where($where)->order('feedback.id DESC')->select();
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
