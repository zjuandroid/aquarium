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
            $where['answerd'] = 0;
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
            $where['answerd'] = 0;
        }else{
            $where['username'] = array('like',"%$key%");
            $where['content'] = array('like',"%$key%");
            $where['_logic'] = 'or';
//            $model = M()->field('t1.username, t2.id, t2.userid, t2.content')->table('member as t1')->join('feedback as t2 on t1.id=t2.userid');
            $member = M('member');
            $model = $member->join('RIGHT JOIN feedback ON member.id = feedback.userid')->select();
            dump($model);
        }

        $count = $model->where($where)->count();
        $pagecount = 15;
        $page = new \Extend\Page($count , $pagecount);
        $show = $page->show();
        $this->assign('page',$show);

        $feedback = $model->limit($page->firstRow.','.$page->listRows)->where($where)->order('id ASC')->select();
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
            $data = M('feedback')->where('id=' . $id)->select();
            if($data) {
                $data = $data[0];
            }
            $this->assign('model', $data);
            $this->display();
        }
        if(IS_POST) {
            $condition['id'] = I('post.id');
            $data['answerd'] = 1;
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

    function test() {
        $data = M('feedback')->where('id=1')->select();
            dump($data);
    }

    /**
     * 添加分类
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('category')->select();
            $cate = getSortedCategory($model);

            $this->assign('cate',$cate);
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Category");
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {

                if ($model->add()) {
                    $this->success("分类添加成功", U('category/index'));
                } else {
                    $this->error("分类添加失败");
                }
            }
        }
    }
    /**
     * 更新分类信息
     * @param  [type] $id [分类ID]
     * @return [type]     [description]
     */
    public function update()
    {
        //默认显示添加表单
        if (!IS_POST) {
            $model = M('feedback')->find(I('id',"addslashes"));
          
            $this->assign('cate',getSortedCategory(M('category')->select()));
            $this->assign('model',$model);
            $this->display();
        }
        if (IS_POST) {
            $model = D("Category");
            if (!$model->create()) {
                $this->error($model->getError());
            }else{
             //   dd(I());die;
                if ($model->save()) {
                    $this->success("分类更新成功", U('category/index'));
                } else {
                    $this->error("分类更新失败");
                }        
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
    		$id = intval($id);
        $model = M('category');
        //查询属于这个分类的文章
        $posts = M('post')->where("cate_id= %d",$id)->select();
        if($posts){
            $this->error("禁止删除含有文章的分类");
        }
        //禁止删除含有子分类的分类
        $hasChild = $model->where("pid= %d",$id)->select();
        if($hasChild){
            $this->error("禁止删除含有子分类的分类");
        }
        //验证通过
        $result = $model->delete($id);
        if($result){
            $this->success("分类删除成功", U('category/index'));
        }else{
            $this->error("分类删除失败");
        }
    }
}
