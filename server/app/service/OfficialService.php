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

class OfficialService
{

    protected $config = [];
    public function __construct()
    {
        $config = ConfigProviders::get('official');
        if (empty($config['appid']) || empty($config['secret'])) {
            throw new \Exception('请先完成配置');
        }
        $this->config = $config;
    }


    /**
     * 公众号登录
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-09
     */
    public function login($code)
    {
        if (empty($code)) {
            throw new \Exception('code不能为空');
        }
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->config['appid']}&secret={$this->config['secret']}&code={$code}&grant_type=authorization_code";
        $client = new Client();
        $response = $client->get($url);
        $result = (array) json_decode($response->getBody(), true);
        if (isset($result['errcode'])) {
            throw new \Exception('登录失败: ' . $result['errmsg']);
        }
        return $result;
    }


    /**
     * 获取用户信息
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-09
     */
    public function getWechatUserInfo($token, $openid)
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
        $client = new Client();
        $response = $client->get($url);
        $wxUserInfo = json_decode($response->getBody());
        if (isset($wxUserInfo->errcode)) {
            throw new \Exception("获取微信用户信息失败：{$wxUserInfo->errmsg}【{$wxUserInfo->errcode}】");
        }
        return $wxUserInfo;
    }

}