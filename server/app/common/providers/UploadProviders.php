<?php

namespace app\common\providers;

use app\common\utils\UploadConfig;
use app\model\Upload;
use GuzzleHttp\Client;
use Shopwwi\WebmanFilesystem\Facade\Storage;
use Webman\Http\UploadFile;

class UploadProviders
{
    public static function url($path, $adapter = null)
    {
        if (empty($path)) {
            return '';
        }
        if (is_array($path)) {
            if (count($path) > 1) {
                $url = [];
                foreach ($path as $key => $value) {
                    $url[] = self::getUrl($value, $adapter);
                }
                return $url;
            }
            $path = $path[0];
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path; // 如果已经是完整的URL，直接返回
        }
        return self::getUrl($path, $adapter);
    }

    public static function path($url)
    {
        if (is_array($url)) {
            $data = [];
            if (count($url) === 1) {
                return self::path(current($url));
            }
            foreach ($url as $value) {
                if (filter_var($value, FILTER_SANITIZE_URL) === false) {
                    throw new \Exception('URL地址不合法');
                }
                $parseUrl = parse_url($value);
                $data[] = ltrim($parseUrl['path'], '/');
            }
            return $data;
        } else {
            if (filter_var($url, FILTER_SANITIZE_URL) === false) {
                throw new \Exception('URL地址不合法');
            }
            $parseUrl = parse_url($url);
            $data = ltrim($parseUrl['path'], '/');
            return $data;
        }
    }

    private static function getUrl($path, $adapter = null)
    {
        if ($adapter == null) {
            $adapter = Upload::where('url', $path)->value('adapter');
        }
        if (empty($adapter)) {
            return '';
        }
        $config = ConfigProviders::get('upload');
        $url = Storage::adapter($adapter)->setConfig(UploadConfig::setConfig($config))->url($path);
        return $url;
    }



    public static function saveRemoteFile($url, $uid = 0)
    {
        if (empty($url)) {
            return throw new \Exception('远程文件地址不能为空');
        }
        $config = ConfigProviders::get('upload');
        if (empty($config)) {
            return throw new \Exception(' 请先配置上传参数');
        }
        $client = new Client();
        $response = $client->get($url);
        $body = $response->getBody();
        $file = $body->getContents();
        $urlPath = parse_url($url, PHP_URL_PATH);
        $ext = pathinfo($urlPath, PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        $temp = tempnam(sys_get_temp_dir(), '') . $fileName;
        file_put_contents($temp, $file);
        $file = new UploadFile($temp, $temp, $response->getHeaderLine('Content-Type'), 0);
        $path = 'upload/' . date('Ymd');
        $result = Storage::adapter($config['base']['adapter'])->setConfig(UploadConfig::setConfig($config))->size($config['base']['size'] * 1024 * 1024)->path($path)->upload($file);
        $result = (array) $result;
        if (!$result) {
            return throw new \Exception('文件上传失败，请检查上传配置');
        }
        $model = new Upload();
        $model->title = $result['origin_name'];
        $model->url = $result['file_name'];
        $model->adapter = $result['adapter'];
        $model->size = round($result['size'], 4);
        $model->ext = $result['extension'];
        $model->mime_type = $result['mime_type'];
        $model->uid = $uid;
        $model->type = self::getFileTypeByExtension($result['extension']);
        $model->save();
        return $result['file_url'];

    }


    private static function getFileTypeByExtension($ext)
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