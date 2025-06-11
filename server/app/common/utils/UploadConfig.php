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
namespace app\common\utils;

class UploadConfig
{
    public static function setConfig($config)
    {
        $host = '//' . request()->host();
        $get = fn($key, $field = '', $default = '') =>
            isset($config[$key]) ? ($field ? ($config[$key][$field] ?? $default) : $config[$key]) : $default;

        $result = [
            'enable' => true,
            'default' => $get('base', 'adapter', 'public'),
            'max_size' => 10 * 1024 * 1024, // 10MB
            'ext_yes' => [],
            'ext_no' => [],
            'storage' => [
                'public' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                    'root' => public_path(),
                    'url' => $get('public', 'url', $host),
                ],
                'local' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\LocalAdapterFactory::class,
                    'root' => runtime_path(),
                    'url' => $get('public', 'url', $host),
                ],
                'ftp' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\FtpAdapterFactory::class,
                    'host' => 'ftp.example.com',
                    'username' => 'username',
                    'password' => 'password',
                    'url' => '',
                ],
                'memory' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\MemoryAdapterFactory::class,
                ],
                's3' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                    'credentials' => [
                        'key' => 'S3_KEY',
                        'secret' => 'S3_SECRET',
                    ],
                    'region' => 'S3_REGION',
                    'version' => 'latest',
                    'bucket_endpoint' => false,
                    'use_path_style_endpoint' => false,
                    'endpoint' => 'S3_ENDPOINT',
                    'bucket_name' => 'S3_BUCKET',
                    'url' => '',
                ],
                'minio' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\S3AdapterFactory::class,
                    'credentials' => [
                        'key' => 'S3_KEY',
                        'secret' => 'S3_SECRET',
                    ],
                    'region' => 'S3_REGION',
                    'version' => 'latest',
                    'bucket_endpoint' => false,
                    'use_path_style_endpoint' => true,
                    'endpoint' => 'S3_ENDPOINT',
                    'bucket_name' => 'S3_BUCKET',
                    'url' => '',
                ],
                'oss' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\AliyunOssAdapterFactory::class,
                    'accessId' => $get('oss', 'AccessId'),
                    'accessSecret' => $get('oss', 'AccessSecret'),
                    'bucket' => $get('oss', 'Bucket'),
                    'endpoint' => $get('oss', 'Endpoint'),
                    'url' => $get('oss', 'url'),
                    'isCName' => false,
                    'prefix' => '',
                ],
                'qiniu' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\QiniuAdapterFactory::class,
                    'accessKey' => 'QINIU_ACCESS_KEY',
                    'secretKey' => 'QINIU_SECRET_KEY',
                    'bucket' => 'QINIU_BUCKET',
                    'domain' => 'QINBIU_DOMAIN',
                    'url' => '',
                ],
                'cos' => [
                    'driver' => \Shopwwi\WebmanFilesystem\Adapter\CosAdapterFactory::class,
                    'region' => $get('cos', 'Region'),
                    'app_id' => $get('cos', 'Appid'),
                    'secret_id' => $get('cos', 'SecretId'),
                    'secret_key' => $get('cos', 'SecretKey'),
                    'bucket' => $get('cos', 'Bucket'),
                    'read_from_cdn' => false,
                    'url' => $get('cos', 'url'),
                ],
            ],
        ];

        if ($get('cos', 'PrivateType') === '1') {
            $result['storage']['cos']['signed_url'] = false;
        }

        return $result;
    }
}
