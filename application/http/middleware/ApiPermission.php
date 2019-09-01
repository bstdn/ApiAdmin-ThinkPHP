<?php

namespace app\http\middleware;

use app\util\ReturnCode;

class ApiPermission {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     */
    public function handle($request, \Closure $next) {
        $header = config('apiadmin.cross_domain');
        $appInfo = $request->APP_CONF_DETAIL;
        $apiInfo = $request->API_CONF_DETAIL;
        $allRules = explode(',', $appInfo['app_api']);
        if(!in_array($apiInfo['hash'], $allRules)) {
            return json([
                'code' => ReturnCode::INVALID,
                'msg'  => '非常抱歉，您没有权限这么做',
                'data' => [],
            ])->header($header);
        }

        return $next($request);
    }
}
