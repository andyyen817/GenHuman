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
use app\common\providers\UploadProviders;
use app\model\Bill;
use support\Request;

class BillController extends BaseController
{
    /**
     * 获取列表
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getList(Request $request): mixed
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $data = $request->get();
        $where = [];
        if (isset($data['nickname']) && $data['nickname'] != '') {
            $where[] = ['u.nickname', 'like', '%' . $data['nickname'] . '%'];
        }
        if (isset($data['type']) && $data['type'] != '') {
            $where[] = ['b.type', '=', $data['type']];
        }
        if (isset($data['userId']) && $data['userId'] != '') {
            $where[] = ['b.uid', '=', $data['userId']];
        }
        $list = Bill::alias('b')
            ->join('user u', 'b.uid = u.id')
            ->field('b.*, u.avatar, u.nickname')
            ->where($where)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ])->toArray();
        foreach ($list['data'] as $key => $item) {
            $list['data'][$key]['avatar'] = UploadProviders::url($item['avatar']);
        }
        return $this->successData($list);
    }
}