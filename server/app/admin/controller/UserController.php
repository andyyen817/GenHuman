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
use app\logic\BillLogic;
use app\model\Plans;
use app\model\User;
use support\Request;

class UserController extends BaseController
{
    /**
     * 用户列表
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
            $where[] = ['nickname', 'like', '%' . $data['nickname'] . '%'];
        }
        if (isset($data['userId']) && $data['userId'] != '') {
            $where[] = ['id', '=', $data['userId']];
        }
        $list = User::order('id desc')
            ->where($where)
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        return $this->successData($list);
    }


    /**
     * 用户充值
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-09
     */
    public function recharge(Request $request): mixed
    {
        $data = $request->post();
        $user = User::where('id', $data['id'])->find();
        if (!$user) {
            return $this->error('用户不存在');
        }

        //扣除
        if ($data['type'] == 1) {
            if (!isset($data['points']) || !is_numeric($data['points']) || $data['points'] <= 0) {
                return $this->error(' 扣除点数不合法');
            }
            if ($user->points < $data['points']) {
                return $this->error('扣除点数不能超过用户剩余点数');
            }
            BillLogic::Bill($user->id, 1, $data['points'], '后台扣除点数');
        }

        if ($data['type'] == 2) {
            if ($data['recharge_type'] == 1) {
                if (!isset($data['points']) || !is_numeric($data['points']) || $data['points'] <= 0) {
                    return $this->error(' 充值点数不合法');
                }
                BillLogic::Bill($user->id, 2, $data['points'], '后台充值');
            }
            if ($data['recharge_type'] == 2) {
                $plan = Plans::where('id', $data['plans_id'])->find();
                if (!$plan) {
                    return $this->error('套餐不存在');
                }
                BillLogic::Bill($user->id, 2, ((int) $plan->points + (int) $plan->give), '后台充值套餐');
            }
        }
        return $this->success('操作成功');
    }
}