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
use support\Log;

class YiDingService
{

    protected $token;
    public function __construct()
    {
        $token = ConfigProviders::get('yiding', 'token');
        if (empty($token)) {
            throw new \Exception('请先完成配置');
        }
        $this->token = $token;
    }

    /**
     * 克隆声音
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-27
     */
    public function cloneVoice($name, $audio_url, $channel = 1)
    {
        $url = match ($channel) {
            1 => "https://api.yidevs.com/app/human/human/Voice/clone",
            2 => "https://api.yidevs.com/app/human/human/Voice/deepClone",
            3 => throw new \Exception('未知的音色频道'),
        };
        $cilent = new Client(['verify' => false]);
        $response = $cilent->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => [
                'name' => $name,
                'audio_url' => $audio_url,
                'description' => '克隆音色',
            ]
        ]);
        $res = $response->getBody()->getContents();
        Log::info('克隆音色返回：' . $res);
        $result = json_decode($res, true);
        if (!$result) {
            throw new \Exception($res);
        }
        if ($result['code'] != 200) {
            throw new \Exception($result['msg']);
        }
        return $result['data'];
    }


    /**
     * 场景克隆
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-28
     */
    public function createScene($name, $video_url)
    {
        $url = 'https://api.yidevs.com/app/human/human/Scene/created';
        $cilent = new Client(['verify' => false]);
        $webUrl = ConfigProviders::get('site', 'webUrl');
        $response = $cilent->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => [
                'video_name' => $name,
                'video_url' => $video_url,
                'callback_url' => $webUrl . '/YDNotify/scene',
            ]
        ]);
        $res = $response->getBody()->getContents();
        Log::info('场景音色返回：' . $res);
        $result = json_decode($res, true);
        if (!is_array($result)) {
            throw new \Exception($res);
        }
        if ($result['code'] != 200) {
            throw new \Exception($result['msg']);
        }
        return $result['data']['scene_task_id'];
    }

    /**
     * 合成音频
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-05-29
     */
    public function createAudio($voice_id, $text, $channel = 1)
    {
        $url = match ($channel) {
            1 => "https://api.yidevs.com/app/human/human/Voice/created",
            2 => "https://api.yidevs.com/app/human/human/Voice/deepCreated",
            3 => throw new \Exception('未知的音色频道'),
        };
        $cilent = new Client(['verify' => false]);
        $response = $cilent->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'form_params' => [
                'text' => $text,
                'voice_id' => $voice_id
            ]
        ]);
        $res = $response->getBody()->getContents();
        Log::info('视频合成返回：' . $res);
        $result = json_decode($res, true);
        if (!is_array($result)) {
            throw new \Exception($res);
        }
        if ($result['code'] != 200) {
            throw new \Exception($result['msg']);
        }
        return $result['data']['audio_url'];
    }

    /**
     * 合成视频
     * @author:1950781041@qq.com
     * @Date:2024-11-01
     */
    public function createVideo($task_id, $voice_url, $worksChannel = 1)
    {
        $url = match ((int) $worksChannel) {
            1 => "https://api.yidevs.com/app/human/human/Index/created",
            2 => "https://api.yidevs.com/app/human/human/Musetalk/generate",
            3 => "https://api.yidevs.com/app/human/human/Musetalk/create",
            default => throw new \Exception('未知的作品频道'),
        };

        $url = "https://api.yidevs.com/app/human/human/Index/created";
        $cilent = new Client(['verify' => false]);
        $webUrl = ConfigProviders::get('site', 'webUrl');
        $response = $cilent->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => [
                'scene_task_id' => $task_id,
                'audio_url' => $voice_url,
                'callback_url' => $webUrl . '/YDNotify/video',
            ]
        ]);
        $res = $response->getBody()->getContents();
        $result = json_decode($res, true);
        if (!is_array($result)) {
            throw new \Exception($res);
        }
        if ($result['code'] != 200) {
            throw new \Exception($result['msg']);
        }
        return $result['data']['video_task_id'];
    }


    /**
     * AI文案
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-04
     */
    public function copywrite($title, $size)
    {
        $url = "https://api.yidevs.com/app/human/human/Chat/generate";
        $cilent = new Client(['verify' => false]);
        $response = $cilent->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => [
                'prompt' => '你是一名资深的新媒体文案助手，擅长创作吸引流量的内容。你的任务是根据抖音、百度热搜、小红书、微博热榜上的热门话题，为我提供有创意、有观点、有情绪张力的内容文案，包括：
                事件的简要背景描述
                独到的个人观点或角度分析
                引发讨论或共鸣的金句或评论式文案（适合发微博、小红书等）
                可用于短视频文案的创意开头和结尾句
                请使用年轻化、共情感强、代入感强的语言风格，并能根据平台调性（如抖音适合口语化、节奏快，小红书适合精致生活风格）灵活调整。
                如果我没有提供具体话题,请你根据今天热榜自动选取1~2个热点进行创作',
                'content' => '根据选题' . $title . '，生成内容,要求：内容有情绪、有立场，符合抖音/微博/小红书语境。字数控制在' . $size . '字左右'
            ]
        ]);
        $res = $response->getBody()->getContents();
        $result = json_decode($res, true);
        if (!is_array($result)) {
            throw new \Exception($res);
        }
        if ($result['code'] != 200) {
            throw new \Exception($result['msg']);
        }
        return $result['data'];
    }

    /**
     * 通用工具类
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-20
     */
    public function yiDingUtil($type, $data)
    {
        $url = match ($type) {
            'oralCopy' => 'https://api.yidevs.com/app/human/human/Chat/generate',
            'facialFusion' => 'https://api.yidevs.com/app/human/human/Tool/facialFusion',
            default => throw new \Exception('未知的工具类型'),
        };
        // $url = "https://api.yidevs.com/app/human/human/Tool/facialFusion";
        $cilent = new Client(['verify' => false]);
        $response = $cilent->request('POST', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token
            ],
            'json' => $data
        ]);
        $res = $response->getBody()->getContents();
        Log::info('请求壹定结果' . $res);
        $result = json_decode($res, true);
        if (isset($result['code']) && $result['code'] != 200) {
            throw new \Exception($result['msg']);
        }
        if (!is_array($result)) {
            throw new \Exception($res);
        }
        return $result['data'];
    }

}