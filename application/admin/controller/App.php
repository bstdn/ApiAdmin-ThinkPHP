<?php

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminGroup;
use app\model\AdminList;
use app\util\ReturnCode;
use app\util\Strs;

class App extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');
        $obj = new AdminApp();
        if(strlen($status)) {
            $obj = $obj->where('app_status', $status);
        }
        if($type) {
            switch($type) {
                case 1:
                    $obj = $obj->where('app_id', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('app_name', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->order('app_add_time', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total'],
        ]);
    }

    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminApp::update([
            'id'         => $id,
            'app_status' => $status,
        ]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $appInfo = AdminApp::get($id);
        cache('AccessToken:' . $appInfo['app_secret'], null);
        if($oldWiki = cache('WikiLogin:' . $id)) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();
    }

    public function add() {
        $postData = $this->request->post();
        $data = [
            'app_id'       => $postData['app_id'],
            'app_secret'   => $postData['app_secret'],
            'app_name'     => $postData['app_name'],
            'app_info'     => $postData['app_info'],
            'app_group'    => $postData['app_group'],
            'app_add_time' => time(),
            'app_api'      => '',
            'app_api_show' => '',
        ];
        if(isset($postData['app_api']) && $postData['app_api']) {
            $appApi = [];
            $data['app_api_show'] = json_encode($postData['app_api']);
            foreach($postData['app_api'] as $value) {
                $appApi = array_merge($appApi, $value);
            }
            $data['app_api'] = implode(',', $appApi);
        }
        $res = AdminApp::create($data);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        $data = [
            'app_secret'   => $postData['app_secret'],
            'app_name'     => $postData['app_name'],
            'app_info'     => $postData['app_info'],
            'app_group'    => $postData['app_group'],
            'app_api'      => '',
            'app_api_show' => '',
        ];
        if(isset($postData['app_api']) && $postData['app_api']) {
            $appApi = [];
            $data['app_api_show'] = json_encode($postData['app_api']);
            foreach($postData['app_api'] as $value) {
                $appApi = array_merge($appApi, $value);
            }
            $data['app_api'] = implode(',', $appApi);
        }
        $res = AdminApp::update($data, ['id' => $postData['id']]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        $appInfo = AdminApp::get($postData['id']);
        cache('AccessToken:' . $appInfo['app_secret'], null);
        if($oldWiki = cache('WikiLogin:' . $postData['id'])) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();
    }

    public function del() {
        $id = $this->request->get('id');
        if(!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $appInfo = AdminApp::get($id);
        cache('AccessToken:' . $appInfo['app_secret'], null);
        AdminApp::destroy($id);
        if($oldWiki = cache('WikiLogin:' . $id)) {
            cache('WikiLogin:' . $oldWiki, null);
        }

        return $this->buildSuccess();
    }

    public function getAppInfo() {
        $apiArr = AdminList::all();
        foreach($apiArr as $api) {
            $res['apiList'][$api['group_hash']][] = $api;
        }
        $groupArr = AdminGroup::all()->toArray();
        $res['groupInfo'] = array_column($groupArr, 'name', 'hash');
        $id = $this->request->get('id', 0);
        if($id) {
            $appInfo = AdminApp::get($id)->toArray();
            $res['app_detail'] = json_decode($appInfo['app_api_show'], true);
        } else {
            $res['app_id'] = mt_rand(1, 9) . Strs::randString(7, 1);
            $res['app_secret'] = Strs::randString(32);
        }

        return $this->buildSuccess($res);
    }

    public function refreshAppSecret() {
        $data['app_secret'] = Strs::randString(32);

        return $this->buildSuccess($data);
    }
}
