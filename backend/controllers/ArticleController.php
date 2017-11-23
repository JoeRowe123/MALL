<?php
/**
 * Created by PhpStorm.
 * User: DELL
 * Date: 2017/11/3
 * Time: 17:40
 */

namespace backend\controllers;


use backend\models\Article;
use backend\models\ArticleDetail;
use frontend\filters\RbacFilter;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller
{
    public function actionList(){
        //创建查询器（对象）
        $query = Article::find()->where(['>','status',-1]);
        //创建分页工具对象
        $pager = new Pagination();
        //统计品牌数据总条数
        $pager->totalCount = $query->count();
        //设置每页显示条数
        $pager->pageSize = 6;
        //按分页条件查询数据
        $model = $query->limit($pager->limit)->offset($pager->offset)->all();
//        var_dump($model);die;
        //将数据分配到视图，并显示
        return $this->render('list',['model'=>$model,'pager'=>$pager]);
    }
    //添加文章
    public function actionAdd(){
        $article = new Article();
        $detail = new ArticleDetail();
        $request = new Request();
        //判断请求方式，决定功能
        if ($request->isPost){
            //获取表单提交的数据
            $article->load($request->post());
            //文本框过滤
            $article->intro = htmlspecialchars($article->intro);
            //验证
            if ($article->validate()){
                //确认添加时间
                $article->create_time = time();
                //将文章内容保存到详情表中
                $detail->content = $article->content;
                //获取添加到文章表的id
//                $id = \Yii::$app->db->getLastInsertID();
                //将字段分表保存到表中
                $article->save();
                //让文章表id与详情表id统一
                $detail->article_id = $article->id;
                $detail->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect('list.html');
            }
        }
        //展示添加视图
        return $this->render('add',['model'=>$article]);
    }
    //查看文章详情
    public function actionShow($id){
        //根据id查询文章内容
        $content = ArticleDetail::findOne(['article_id'=>$id]);
        //将内容展示到视图
        return $this->render('show',['content'=>$content]);
    }
    //修改文章
    public function actionEdit($id){
        $article = Article::findOne(['id'=>$id]);
        $detail = ArticleDetail::findOne(['article_id'=>$id]);
        $article->content = $detail->content;
        $request = new Request();
        //判断请求方式，决定功能
        if ($request->isPost){
            //获取表单提交的数据
            $article->load($request->post());
            //文本框过滤
            $article->intro = htmlspecialchars($article->intro);
            //验证
            if ($article->validate()){
                //将文章内容保存到详情表中
                $detail->content = $article->content;
                //获取添加到文章表的id
//                $id = \Yii::$app->db->getLastInsertID();
                //让文章表id与详情表id统一
                $detail->article_id = $id;
                //将字段分表保存到表中
                $article->save();
                $detail->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect('list.html');
            }
        }
        //展示添加视图
        return $this->render('add',['model'=>$article]);
    }
    //删除文章
    public function actionDelete($id){
        $result = Article::updateAll(['status'=>-1],['id'=>$id]);
        if ($result){
            echo 1;
        }else{
            echo json_encode('删除失败');
        }
    }
    public function behaviors(){
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
//                'only'=>['Add'],
//                'except'=>[]
            ]
        ];
    }
}