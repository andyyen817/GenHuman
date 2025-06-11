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
use app\model\Category;
use support\Request;

class CategoryController extends BaseController
{
    /**
     * 获取文章分类
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getList(Request $request)
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $list = Category::order('id desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        return $this->successData($list);
    }


    /**
     * 获取选项分类
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-04
     */
    public function getOptions(Request $request)
    {
        $categories = Category::where('status', 1)
            ->field('id as value, title as label, status, sort')
            ->order('sort asc')
            ->select();
        return $this->successData($categories);
    }


    /**
     * 添加或编辑文章分类
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function saveCategory(Request $request)
    {
        $data = $request->post();
        if (isset($data['id']) && $data['id']) {
            $category = Category::find($data['id']);
            if (!$category) {
                return $this->error('文章分类不存在');
            }
        } else {
            $category = new Category();
        }
        $category->title = $data['title'];
        $category->status = isset($data['status']) ? $data['status'] : 1;
        $category->sort = isset($data['sort']) ? $data['sort'] : 0;
        $category->save();
        return $this->success('保存成功');
    }


    /**
     * 获取文章分类详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getDetail(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->error('缺少文章分类ID');
        }
        $category = Category::find($id);
        if (!$category) {
            return $this->error('文章分类不存在');
        }
        return $this->successData($category);
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
            return $this->error('缺少文章分类ID');
        }
        $category = Category::find($id);
        if (!$category) {
            return $this->error('文章分类不存在');
        }
        $category->delete();
        return $this->success('删除成功');
    }
}