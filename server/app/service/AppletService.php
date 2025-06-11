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
use GuzzleHttp\Client;

class AppletService
{

    protected $config = [];
    public function __construct()
    {
        $this->config = ConfigProviders::get('applet');
    }
    /**
     * 小程序登录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-24
     */
    public function login($code)
    {
        $appletConfig = $this->config;
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid={$appletConfig["appid"]}&secret={$appletConfig["secret"]}&js_code=$code&grant_type=authorization_code";
        $client = new Client();
        $res = $client->request('GET', $url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ])->getBody()->getContents();
        $res = json_decode($res, true);
        if (!isset($res['openid'])) {
            throw new \Exception($res['errmsg']);
        }
        return $res;
    }

}