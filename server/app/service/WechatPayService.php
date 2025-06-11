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

namespace app\service;

use app\common\providers\ConfigProviders;
use Yansongda\Pay\Pay;

class WechatPayService
{

    protected $config = [];
    public function __construct()
    {
        $config = ConfigProviders::get('payWechat');
        if (empty($config['mchId']) || empty($config['paySignKey']) || empty($config['apiclientKey']) || empty($config['apiclientCert'])) {
            throw new \Exception('微信支付配置不完整，请检查配置文件');
        }
        $this->config = $config;
    }

    public function getConfig()
    {
        $url = ConfigProviders::get('site', 'webUrl');
        $appletAppid = ConfigProviders::get('applet', 'appid');
        $mpAppid = ConfigProviders::get('official', 'appid');
        // $fileurl = runtime_path('pay/apiclient_key.pem');
        // if (!is_dir(dirname($fileurl))) {
        //     mkdir(dirname($fileurl), 0755, true);
        //     $apiclientCertUrl = file_put_contents($fileurl, $this->config['apiclientCert']);
        // }
        return [
            'wechat' => [
                'default' => [
                    'mch_id' => $this->config['mchId'],
                    // 「选填」v2商户私钥
                    'mch_secret_key_v2' => '',
                    // 「必填」v3 商户秘钥
                    'mch_secret_key' => $this->config['paySignKey'],
                    // 「必填」商户私钥 字符串或路径
                    'mch_secret_cert' => $this->config['apiclientKey'],
                    // 「必填」商户公钥证书路径
                    'mch_public_cert_path' => $this->config['apiclientCert'],
                    // 「必填」微信回调url
                    'notify_url' => $url . '/Notify/wechat',
                    // 「选填」公众号 的 app_id
                    'mp_app_id' => $mpAppid,
                    // 「选填」小程序 的 app_id
                    'mini_app_id' => $appletAppid,
                    'mode' => Pay::MODE_NORMAL,
                ]
            ],
            'logger' => [
                'enable' => false,
                'file' => runtime_path('/logs/pay.log'),
                'level' => 'info',
                'type' => 'single',
                'max_file' => 30,
            ],
            'http' => [
                'timeout' => 5.0,
                'connect_timeout' => 5.0,
            ],
        ];
    }

    public function applet($order_no, $price, $openid, $desc = "购买商品")
    {
        $config = $this->getConfig();
        Pay::config($config);
        $order = [
            'out_trade_no' => $order_no,
            'description' => $desc,
            'amount' => [
                'total' => $price * 10 * 10,
                'currency' => 'CNY',
            ],
            'payer' => [
                'openid' => $openid,
            ]
        ];
        $result = Pay::wechat()->mini($order);
        return $result->toArray();
    }


    public function official($order_no, $price, $openid, $desc = "购买商品")
    {
        $config = $this->getConfig();
        Pay::config($config);
        $order = [
            'out_trade_no' => $order_no,
            'description' => $desc,
            'amount' => [
                'total' => $price * 10 * 10,
                'currency' => 'CNY',
            ],
            'payer' => [
                'openid' => $openid,
            ]
        ];
        $result = Pay::wechat()->mp($order);
        return $result->toArray();
    }

}