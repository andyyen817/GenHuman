<?php
// +----------------------------------------------------------------------
// | 贵州猿创科技 [致力于通过产品和服务，帮助创业者高效化开拓市场]
// +----------------------------------------------------------------------
// | Copyright(c)2019~2024 https://xhadmin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed 这不是一个自由软件，不允许对程序代码以任何形式任何目的的再发行
// +----------------------------------------------------------------------
// | Author:贵州猿创科技<416716328@qq.com>|<Tel:18786709420>
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\BaseController;
use app\model\Article;
use support\Request;

class ArticleController extends BaseController
{
    /**
     * 获取文章
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getList(Request $request)
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $list = Article::with('category')->order('id desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        return $this->successData($list);
    }


    /**
     * 添加或编辑文章
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function saveArticle(Request $request)
    {
        $data = $request->post();
        if (isset($data['id']) && $data['id']) {
            $article = Article::find($data['id']);
            if (!$article) {
                return $this->error('文章不存在');
            }
        } else {
            $article = new Article();
        }
        $article->title = $data['title'];
        $article->content = $data['content'] ?? '';
        $article->desc = $data['desc'] ?? '';
        $article->sort = $data['sort'];
        $article->status = $data['status'];
        $article->cover = $data['cover'];
        $article->category_id=$data['category_id'];
        $article->recommend=$data['recommend'];
        $article->save();
        return $this->success('保存成功');
    }


    /**
     * 获取文章详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getDetail(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->error('缺少文章ID');
        }
        $article = Article::find($id);
        if (!$article) {
            return $this->error('文章不存在');
        }
        return $this->successData($article);
    }


    /**
     * 删除
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function delete(Request $request)
    {
        $id = $request->post('id');
        if (!$id) {
            return $this->error('缺少文章ID');
        }
        $article = Article::find($id);
        if (!$article) {
            return $this->error('文章不存在');
        }
        $article->delete();
        return $this->success('删除成功');
    }
}