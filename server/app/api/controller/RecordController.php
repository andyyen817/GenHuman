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
use app\model\UseAppRecord;
use support\Request;

class RecordController extends BaseController
{


    public function getList(Request $request)
    {
        $data = $request->get();
        $page = $data['page'] ?? 1;
        $limit = $data['limit'] ?? 10;
        $where = [];
        $where[] = ['uid', '=', $request->uid];
        if (isset($data['type']) && $data['type']) {
            $where[] = ['type', '=', $data['type']];
        }
        $list = UseAppRecord::where($where)
            ->order('id desc')
            ->select();
        return $this->successData($list);
    }

    /**
     * 删除
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-24
     */
    public function del(Request $request)
    {
        $id = $request->post('id');
        if (!$id) {
            return $this->error('参数错误');
        }
        $record = UseAppRecord::where('id', $id)->find();
        if (!$record) {
            return $this->error('数据不存在');
        }
        if ($record->delete()) {
            return $this->success('删除成功');
        }
        return $this->error('删除失败');
    }

}