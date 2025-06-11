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
use app\model\Plans;
use support\Request;

class PlansController extends BaseController
{
    /**
     * 获取套餐
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getList(Request $request)
    {

        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $list = Plans::order('id desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page
            ]);
        return $this->successData($list);
    }


    /**
     * 添加或编辑套餐
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function savePlans(Request $request)
    {
        $data = $request->post();
        if (isset($data['id']) && $data['id']) {
            $plan = Plans::find($data['id']);
            if (!$plan) {
                return $this->error('套餐不存在');
            }
        } else {
            $plan = new Plans();
        }
        $plan->title = $data['title'];
        $plan->price = $data['price'];
        $plan->points = $data['points'];
        $plan->give = $data['give'];
        $plan->original_price = $data['original_price'];
        $plan->sort = isset($data['sort']) ? $data['sort'] : 0;
        $plan->status = isset($data['status']) ? $data['status'] : 1;
        $plan->save();
        return $this->success('保存成功');
    }


    /**
     * 获取套餐详情
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getDetail(Request $request)
    {
        $id = $request->get('id');
        if (!$id) {
            return $this->error('缺少套餐ID');
        }
        $plan = Plans::find($id);
        if (!$plan) {
            return $this->error('套餐不存在');
        }
        return $this->successData($plan);
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
            return $this->error('缺少套餐ID');
        }
        $plan = Plans::find($id);
        if (!$plan) {
            return $this->error('套餐不存在');
        }
        $plan->delete();
        return $this->success('删除成功');
    }

    /**
    * 获取选项
    * @author:下次一定
    * @email:1950781041@qq.com
    * @Date:2025-06-09
    */
    public function getOptions(Request $request)
    {
        $list = Plans::order('sort asc')->where('status',1)->field('id as value,title as label')->select();
        return $this->successData($list);
    }
}