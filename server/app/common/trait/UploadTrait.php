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
namespace app\common\trait;

use app\common\providers\ConfigProviders;
use app\common\utils\JsonUtil;
use app\common\utils\UploadConfig;
use app\model\Upload;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use support\Request;


trait UploadTrait
{
    use JsonUtil;
    protected $fileTypeArr = [
        'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'],
        'audio' => ['mp3', 'wav', 'aac', 'flac', 'ogg', 'm4a'],
        'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
        'document' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'csv'],
    ];
    public function upload(Request $request)
    {
        $file = $request->file('file');
        if (!$file) {
            return $this->error('请上传文件');
        }
        $fileType = $request->post('file_type', 'image');
        if (!in_array($fileType, ['image', 'audio', 'video', 'document'])) {
            return $this->error('不支持的文件类型');
        }
        // $extension = strtolower($file);

        // if (!in_array($extension, $this->fileTypeArr[$fileType])) {
        //     return $this->error('不支持的文件格式');
        // }

        $config = ConfigProviders::get('upload');
        if (empty($config)) {
            return $this->error('请先配置上传参数');
        }
        $path = 'upload/' . date('Ymd');
        try {
            $result = Storage::adapter($config['base']['adapter'])
                ->setConfig(UploadConfig::setConfig($config))->size($config['base']['size'] * 1024 * 1024)
                ->path($path)
                ->upload($file);
            $result = (array) $result;
            $model = new Upload();
            $model->title = $result['origin_name'];
            $model->url = $result['file_name'];
            $model->adapter = $result['adapter'];
            $model->size = round($result['size'], 4);
            $model->ext = $result['extension'];
            $model->mime_type = $result['mime_type'];
            if ($request->source == 'admin') {
                $model->admin_uid = $request->uid ?? 0;
            } else {
                $model->uid = $request->uid ?? 0;
            }
            $model->type = $this->getFileTypeByExtension($result['extension']);
            $model->save();
        } catch (\Throwable $e) {
            return $this->error($e->getMessage());
        }
        return $this->successData($result);
    }



    private function getFileTypeByExtension($ext)
    {
        $ext = strtolower(trim($ext)); // 统一格式
        // 类型映射配置
        $typeMap = [
            1 => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'tiff'], // 图片
            2 => ['mp3', 'wav', 'aac', 'flac', 'ogg', 'm4a'],                // 音频
            3 => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],         // 视频
            4 => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'csv'], // 文档
        ];
        foreach ($typeMap as $type => $extensions) {
            if (in_array($ext, $extensions)) {
                return $type;
            }
        }
        return 5; // 其他
    }


}