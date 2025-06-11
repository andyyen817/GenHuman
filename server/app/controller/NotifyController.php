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

namespace app\controller;

use app\logic\BillLogic;
use app\model\Order;
use app\model\Plans;
use app\service\WechatPayService;
use support\Log;
use support\Request;
use support\Response;
use think\facade\Db;
use Yansongda\Pay\Pay;

class NotifyController
{
    public function wechat(Request $request)
    {
        $service = new WechatPayService();
        $config = $service->getConfig();
        Pay::config($config);
        $result = Pay::wechat()->callback($request->post())->get();
        Log::info('微信支付回调', $result);
        $order = Order::where('order_no', $result['resource']['ciphertext']['out_trade_no'])->find();

        if (!$order) {
            return new Response(200, [], 'success');
        }
        if ($order->status != 0) {
            return new Response(200, [], 'success');
        }
        if ($result['resource']['ciphertext']['trade_state'] != 'SUCCESS') {
            Log::error('微信支付回调失败', $result);
            return new Response(200, [], 'fail');
        }
        Db::startTrans();
        try {
            $order->status = 1;
            $order->pay_time = date('Y-m-d H:i:s');
            $order->save();
            $plans = Plans::where('id', $order->plan_id)->find();
            BillLogic::Bill($order->uid, 2, $order->points, '购买套餐：' . $plans->title);
            Db::commit();
            return new Response(200, [], 'success');
        } catch (\Exception $e) {
            Db::rollback();
            Log::error('微信支付回调异常', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return new Response(500, [], 'fail');
        }

    }


}
