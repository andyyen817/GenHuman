<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Static file settings
 */
return [
    'enable' => false,  // 關閉自動靜態文件處理，讓自定義路由優先
    'middleware' => [     // Static file Middleware
        //app\middleware\StaticFile::class,
    ],
];