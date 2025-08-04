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
use app\logic\BillLogic;
use app\model\App;
use app\model\Category;
use app\model\UseAppRecord;
use app\model\User;
use app\service\YiDingService;
use support\Request;

class AppController extends BaseController
{

    protected $noLogin = [
        'getList',
    ];


    /**
     * 应用工具的请求参数
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-20
     */
    protected $util = [
        'oralCopy' => ['size', 'title'],
        'facialFusion' => ['face_url', 'template_url'],
        'photoHD' => ['photo_url'],
        'oldPhotoRestoration' => ['photo_url'],
        'hairStyle' => ['hair_url', 'hair_type'],
    ];

    /**
     * 立马返回结果的应用工具
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-20
     */
    protected $immediate = [
        'oralCopy',
        'facialFusion',
        'photoHD',
        'oldPhotoRestoration',
        'hairStyle',
    ];
    /**
     * 需要保存本地的应用工具
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-24
     */
    protected $localSave = [
        'facialFusion',
        'photoHD',
        'oldPhotoRestoration',
        'hairStyle',
    ];

    public function getList(Request $request)
    {
        $list = App::where('status', 1)
            ->order('sort asc, id desc')
            ->select();
        return $this->successData($list);
    }

    public function getDetail(Request $request)
    {
        $id = $request->get('id', 0);
        $type = $request->get('type', '');
        if (!$id && !$type) {
            return $this->error('参数错误');
        }
        $where = [];
        if ($id) {
            $where[] = ['id', '=', $id];
        }
        if ($type) {
            $where[] = ['type', '=', $type];
        }
        $app = App::where($where)->find();
        if (!$app) {
            return $this->error('应用不存在');
        }
        $app->category = Category::find($app->category_id);
        return $this->successData($app);
    }


    /**
     * 通用工具方法
     * @author:下次一定
     * @email:1950781041@qq.com
     * @Date:2025-06-20
     */
    public function yiDingUtil(Request $request)
    {
        $app_id = $request->post('app_id');
        if (!$app_id) {
            return $this->error('应用ID不能为空');
        }
        $app = App::where('id', $app_id)->find();
        if (!$app) {
            return $this->error('应用不存在');
        }
        $data = $request->post();

        if (!isset($this->util[$app->type])) {
            return $this->error('不支持的工具类型');
        }
        $fields = $this->util[$app->type];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return $this->error("参数不能为空");
            }
        }
        $user = User::where('id', $request->uid)->find();
        if ($user->points < $app->points) {
            return $this->error('算力点不足');
        }
        $json = $this->combinedData($data, $fields, $app->type, $app);
        $service = new YiDingService();
        try {
            $record = new UseAppRecord();
            $record->uid = $request->uid;
            $record->app_id = $app->id;
            $record->type = $app->type;
            $record->points = $app->points;
            $record->req_content = json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $result = $service->yiDingUtil($app->type, $json);
            $record->res_content = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (in_array($app->type, $this->immediate)) {
                $record->status = 2;
                BillLogic::Bill($request->uid, 1, $app->points, $app->title);
            } else {
                $record->status = 1;
            }
            if (in_array($app->type, $this->localSave)) {
                $url = UploadProviders::saveRemoteFile($result['image_url']);
                $result['image_url'] = $url;
                $record->result = $url;
            } else {
                $record->result = $result;
            }
            $record->rep_content = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $record->save();
            return $this->successData($result);
        } catch (\Exception $e) {
            $record->status = 3;
            $record->error = $e->getMessage();
            $record->save();
            return $this->error($e->getMessage());
        }
    }

    private function combinedData($data, $fields, $type, $app)
    {
        $result = [];
        switch ($type) {
            case 'oralCopy':
                $content = preg_replace_callback('/\$\{(\w+)\}/', function ($matches) use ($data) {
                    $key = $matches[1];
                    return $data[$key] ?? '';
                }, $app->content_instruct);
                $result = [
                    'prompt' => $app->role_instruct,
                    'content' => $content
                ];
                break;
            default:
                $result = array_intersect_key($data, array_flip($fields));
                break;
        }
        return $result;
    }
}