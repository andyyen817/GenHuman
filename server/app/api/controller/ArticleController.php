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

namespace app\api\controller;

use app\common\BaseController;
use app\model\Article;
use app\model\Category;
use support\Request;

class ArticleController extends BaseController
{

    
    protected $noLogin=[
        'getList',
        'getDetail',
        'getCategory'
    ];

    /**
     * 获取分类
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-03
     */
    public function getCategory(Request $request)
    {
        $category = Category::where('status', 1)
            ->field('id as value, title as label, status, sort')
            ->order('sort asc')
            ->select();
        return $this->successData($category);
    }

    /**
     * 获取文章列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-03
     */
    public function getList(Request $request)
    {
        $page = $request->input('page', 1);
        $limit = $request->input('limit', 10);
        $data = $request->get();
        $where = [];
        if (isset($data['category_id']) && $data['category_id']) {
            $where[] = ['category_id', '=', $data['category_id']];
        }
        if (isset($data['recommend'])) {
            $where[] = ['recommend', '=', 1];
        }
        $where[] = ['status', '=', 1];
        $list = Article::where($where)
            ->order('sort asc')
            ->page($page, $limit)
            ->select();
        return $this->successData($list);
    }

    /**
     * 获取文章详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-03
     */
    public function getDetail(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return $this->error('文章ID不能为空');
        }
        $article = Article::where('id', $id)->find();
        $article->views += 1;
        $article->save();
        if (!$article || $article->status != 1) {
            return $this->error('文章不存在或已被删除');
        }
        return $this->successData($article);
    }
}