<?php

namespace app\http\middleware;

use app\model\AdminAuthGroup;
use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\util\Enum;
use app\util\ReturnCode;
use app\util\Tools;

class AdminPermission {

    /**
     * @param \think\facade\Request $request
     * @param \Closure $next
     * @return \think\response\Json
     */
    public function handle($request, \Closure $next) {
        $userInfo = $request->API_ADMIN_USER_INFO;
        if(!$this->checkAuth($userInfo['id'], $request->path())) {
            return json([
                'code' => ReturnCode::INVALID,
                'msg'  => '非常抱歉，您没有权限这么做！',
                'data' => [],
            ])->header(config('apiadmin.cross_domain'));
        }

        return $next($request);
    }

    private function checkAuth($uid, $route) {
        $isSupper = Tools::isAdministrator($uid);
        if(!$isSupper) {
            $rules = $this->getAuth($uid);

            return in_array($route, $rules);
        } else {
            return true;
        }
    }

    private function getAuth($uid) {
        $groups = AdminAuthGroupAccess::get(['uid' => $uid]);
        if(isset($groups) && $groups->group_id) {
            $openGroup = (new AdminAuthGroup())->whereIn('id', $groups->group_id)->where(['status' => Enum::isTrue])->select();
            if(isset($openGroup)) {
                $openGroupArr = [];
                foreach($openGroup as $group) {
                    $openGroupArr[] = $group->id;
                }
                $allRules = (new AdminAuthRule())->whereIn('group_id', $openGroupArr)->select();
                if(isset($allRules)) {
                    $rules = [];
                    foreach($allRules as $rule) {
                        $rules[] = $rule->url;
                    }
                    $rules = array_unique($rules);

                    return $rules;
                } else {
                    return [];
                }
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
}
