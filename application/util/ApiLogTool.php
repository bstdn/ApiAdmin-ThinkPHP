<?php

namespace app\util;

use think\facade\Env;

class ApiLogTool {

    private static $appInfo   = 'null';
    private static $apiInfo   = 'null';
    private static $request   = 'null';
    private static $response  = 'null';
    private static $header    = 'null';
    private static $userInfo  = 'null';
    private static $separator = ' | ';

    public static function setAppInfo($data) {
        self::$appInfo =
            (isset($data['app_id']) ? $data['app_id'] : 'null') . self::$separator .
            (isset($data['app_name']) ? $data['app_name'] : 'null') . self::$separator .
            (isset($data['device_id']) ? $data['device_id'] : 'null');
    }

    public static function setHeader($data) {
        $accessToken = (isset($data['access-token']) && !empty($data['access-token'])) ? $data['access-token'] : 'null';
        $version = (isset($data['version']) && !empty($data['version'])) ? $data['version'] : 'null';
        self::$header = $accessToken . self::$separator . $version;
    }

    public static function setApiInfo($data) {
        self::$apiInfo =
            (isset($data['hash']) ? $data['hash'] : 'null') . self::$separator .
            (isset($data['api_class']) ? $data['api_class'] : 'null');
    }

    public static function setRequest($data) {
        if(is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$request = $data;
    }

    public static function setResponse($data, $code = '') {
        if(is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        self::$response = $code . self::$separator . $data;
    }

    public static function save() {
        $logPath = Env::get('runtime_path') . 'ApiLog' . DIRECTORY_SEPARATOR;
        $logStr = implode(self::$separator, array(
            '[' . date('Y-m-d H:i:s') . ']',
            self::$apiInfo,
            self::$request,
            self::$header,
            self::$response,
            self::$appInfo,
            self::$userInfo,
        ));
        if(!file_exists($logPath)) {
            mkdir($logPath, 0755, true);
        }
        @file_put_contents($logPath . date('YmdH') . '.log', $logStr . "\n", FILE_APPEND);
    }
}
