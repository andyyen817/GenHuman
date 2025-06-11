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
use app\model\Order;
use app\model\Plans;
use app\model\User;
use app\service\WechatPayService;
use support\Request;

class OrderController extends BaseController
{
    /**
     * 创建订单
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function createOrder(Request $request)
    {
        $data = $request->post();
        $plans = Plans::where('id', $data['id'])->find();
        if (!$plans) {
            return $this->error('套餐不存在');
        }
        if ($plans->status != 1) {
            return $this->error('套餐已下架');
        }
        $order_no = date('YmdHis') . $data['id'] . rand(1000, 9999) . rand(1000, 99999);
        $order = new Order();
        $order->uid = $request->uid;
        $order->order_no = $order_no;
        $order->price = $plans->price;
        $order->plan_id = $plans->id;
        $order->status = 0; // 0:待支付, 1:已支付, 2:已取消
        $order->source = $data['source'];
        $order->save();
        $service = new WechatPayService();
        if ($data['source'] == 'applet') {
            $openid = User::where('id', $request->uid)->value('openid');
            $result = $service->applet($order_no, $plans->price, $openid, '购买套餐：' . $plans->title);
        } else if ($data['source'] == 'official') {
            $openid = User::where('id', $request->uid)->value('official_openid');
            $result = $service->official($order_no, $plans->price, $openid, '购买套餐：' . $plans->title);
        } else {
            return $this->error('不支持的支付方式');
        }
        return $this->successData($result);
    }
}