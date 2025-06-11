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
use app\common\providers\UploadProviders;
use app\model\Config;
use support\Request;
class ConfigController extends BaseController
{

    protected $noLogin = ['getConfig'];
    /**
     * 获取配置
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-26
     */
    public function getConfig(Request $request)
    {
        $data = [
            'webName' => '数字人',
            'webLogo' => '',
            'avatar' => '',
            'customerType' => '0',
            'customerUrl' => '',
            'customerCorpId' => '',
            'customerQrcode' => '',
            'aiCwPoint' => 0,
            'scenePoint' => 0,
            'voicePoint' => 0,
            'worksPoint' => 0,
            'headerImg'=>'',
            'voiceChannel'=>1,
            // 'voiceProfessionalPoint'=>0,
            'voiceDeepPoint'=>0,
        ];
        $config = Config::where('id', '>', 0)->select()->toArray();
        $value = [];
        foreach ($config as $item) {
            foreach ($item['value'] as $key => $val) {
                $value[$key] = $val;
            }
        }
        $img = [
            'webLogo',
            'avatar',
            'customerQrcode',
            'headerImg'
        ];
        foreach ($data as $key => $v) {
            if (isset($value[$key])) {
                $data[$key] = $value[$key];
            }
            if (in_array($key, $img)) {
                $data[$key] = UploadProviders::url($data[$key]);
            }
        }

        return $this->successData($data);
    }

}