<?php

namespace app\admin\controller;

use app\model\AdminApp;
use app\model\AdminAppGroup;
use app\util\ReturnCode;

class AppGroup extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');
        $obj = new AdminAppGroup();
        if(strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if($type) {
            switch($type) {
                case 1:
                    $obj = $obj->where('hash', $keywords);
                    break;
                case 2:
                    $obj = $obj->whereLike('name', "%{$keywords}%");
                    break;
            }
        }
        $listObj = $obj->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total'],
        ]);
    }

    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminAppGroup::update([
            'id'     => $id,
            'status' => $status,
        ]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function add() {
        $postData = $this->request->post();
        $res = AdminAppGroup::create($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        $res = AdminAppGroup::update($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function del() {
        $hash = $this->request->get('hash');
        if(!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $has = (new AdminApp())->where(['app_group' => $hash])->count();
        if($has) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '当前分组存在' . $has . '个应用，禁止删除');
        }
        AdminAppGroup::destroy(['hash' => $hash]);

        return $this->buildSuccess();
    }

    public function getAll() {
        $listInfo = (new AdminAppGroup())->where(['status' => 1])->select();

        return $this->buildSuccess([
            'list' => $listInfo,
        ]);
    }
}
