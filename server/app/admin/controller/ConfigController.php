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
use app\model\Config as ConfigModel;
use support\Request;

class ConfigController extends BaseController
{
    protected $noLogin = ['getSiteConfig'];
    protected $img = [
        'webLogo',
        'avatar',
        'customerQrcode',
        'headerImg'
    ];
    /**
     * 编辑配置
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-22
     */
    public function save(Request $request)
    {
        $name = $request->post('key');
        $value = $request->post('value');
        $config = ConfigModel::where('key', $name)->find();
        foreach ($this->img as $item) {
            if (isset($value[$item]) && is_array($value[$item])) {
                $value[$item] = UploadProviders::path($value[$item]);
            }
        }
        if (!$config) {
            $config = new ConfigModel();
            $config->key = $name;
        }
        $config->value = $value;
        $config->save();
        return $this->success('保存成功');
    }

    /**
     * 获取配置
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-23
     */
    public function getConfig(Request $request)
    {
        $name = $request->get('key');
        $data = ConfigModel::where('key', $name)->find();
        $value = $data ? $data->value : [];
        foreach ($this->img as $item) {
            if (isset($value[$item]) && is_string($value[$item])) {
                $value[$item] = UploadProviders::url($value[$item]);
            }
        }
        return $this->successData($value);
    }


    /**
     * 获取站点默认配置
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-30
     */
    public function getSiteConfig()
    {
        $data = ConfigModel::where('key', 'site')->find();
        if (empty($data)) {
            $value['webName'] = 'GenHuman';
            $value['webLogo'] = '/static/logo.svg';
            $value['webUrl'] = '//' . request()->host();
        } else {
            $value = $data ? $data->value : [];
            foreach ($this->img as $item) {
                if (isset($value[$item]) && is_string($value[$item])) {
                    $value[$item] = UploadProviders::url($value[$item]);
                }
            }
        }
        return $this->successData($value);
    }
}