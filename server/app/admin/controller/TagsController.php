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
use app\model\Scene;
use app\model\Tags;
use support\Request;

class TagsController extends BaseController
{
    /**
     * 获取单页
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-04
     */
    public function getTags(Request $request)
    {
        $key = $request->get('key', '');
        $detail = Tags::where('key', $key)->find();
        return $this->successData($detail);
    }

    /**
     * 保存
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-04
     */
    public function saveTags(Request $request)
    {
        $data = $request->post();
        if (isset($data['id']) && $data['id']) {
            $tags = Tags::where('id', $data['id'])->find();
            if (!$tags) {
                return $this->error('标签不存在');
            }
        } else {
            $tags = new Tags();
        }
        $tags->key = $data['key'];
        $tags->title = $data['title'];
        $tags->content = $data['content'] ?? '';
        $tags->save();
        return $this->success('保存成功');
    }
}