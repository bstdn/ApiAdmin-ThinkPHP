<?php

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminFields;
use app\model\AdminList;
use app\util\ReturnCode;
use think\facade\Env;

class InterfaceList extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');
        $obj = new AdminList();
        if(strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if($type) {
            switch($type) {
                case 1:
                    $obj = $obj->where('hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('info', "%{$keywords}%");
                    break;
                case 3:
                    $obj = $obj->whereLike('api_class', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->order('id', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total'],
        ]);
    }

    public function changeStatus() {
        $hash = $this->request->get('hash');
        $status = $this->request->get('status');
        $res = AdminList::update([
            'status' => $status,
        ], [
            'hash' => $hash,
        ]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        cache('ApiInfo:' . $hash, null);

        return $this->buildSuccess();
    }

    public function add() {
        $postData = $this->request->post();
        if(!preg_match("/^[A-Za-z0-9_\/]+$/", $postData['api_class'])) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '真实类名只允许填写字母，数字和/');
        }
        $res = AdminList::create($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        if(!preg_match("/^[A-Za-z0-9_\/]+$/", $postData['api_class'])) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '真实类名只允许填写字母，数字和/');
        }
        $res = AdminList::update($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        cache('ApiInfo:' . $postData['hash'], null);

        return $this->buildSuccess();
    }

    public function del() {
        $hash = $this->request->get('hash');
        if(!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $hashRule = AdminApp::all([
            'app_api' => ['like', "%$hash%"],
        ]);
        if($hashRule) {
            $oldInfo = AdminList::get(['hash' => $hash]);
            foreach($hashRule as $rule) {
                $appApiArr = explode(',', $rule->app_api);
                $appApiIndex = array_search($hash, $appApiArr);
                array_splice($appApiArr, $appApiIndex, 1);
                $rule->app_api = implode(',', $appApiArr);
                $appApiShowArrOld = json_decode($rule->app_api_show, true);
                $appApiShowArr = $appApiShowArrOld[$oldInfo->groupHash];
                $appApiShowIndex = array_search($hash, $appApiShowArr);
                array_splice($appApiShowArr, $appApiShowIndex, 1);
                $appApiShowArrOld[$oldInfo->groupHash] = $appApiShowArr;
                $rule->app_api_show = json_encode($appApiShowArrOld);
                $rule->save();
            }
        }
        AdminList::destroy(['hash' => $hash]);
        AdminFields::destroy(['hash' => $hash]);
        cache('ApiInfo:' . $hash, null);

        return $this->buildSuccess();
    }

    public function refresh() {
        $rootPath = Env::get('root_path');
        $apiRoutePath = $rootPath . 'route/apiRoute.php';
        $tplPath = $rootPath . 'application/install/apiRoute.tpl';
        $methodArr = ['*', 'post', 'get'];
        $tplOriginStr = file_get_contents($tplPath);
        $listInfo = AdminList::all(['status' => 1]);
        $tplStr = [];
        foreach($listInfo as $value) {
            array_push($tplStr, 'Route::rule(\'' . addslashes($value->hash) . '\',\'api/' . addslashes($value->api_class) . '\', \'' . $methodArr[$value->method] . '\')->middleware([\'ApiAuth\', \'ApiPermission\', \'RequestFilter\', \'ApiLog\']);');
        }
        $tplOriginStr = str_replace(['{$API_RULE}'], [implode($tplStr, "\n    ")], $tplOriginStr);
        file_put_contents($apiRoutePath, $tplOriginStr);

        return $this->buildSuccess();
    }
}
