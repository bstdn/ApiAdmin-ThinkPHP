<?php

namespace app\admin\controller;

use app\model\AdminAuthGroup;
use app\model\AdminAuthGroupAccess;
use app\model\AdminAuthRule;
use app\model\AdminMenu;
use app\util\Enum;
use app\util\ReturnCode;
use app\util\Tools;

class Auth extends Base {

    public function index() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $keywords = $this->request->get('keywords', '');
        $status = $this->request->get('status', '');
        $obj = new AdminAuthGroup();
        if(strlen($status)) {
            $obj = $obj->where('status', $status);
        }
        if($keywords) {
            $obj = $obj->whereLike('name', "%{$keywords}%");
        }
        $listObj = $obj->order('id', 'DESC')->paginate($limit, false, ['page' => $start])->toArray();

        return $this->buildSuccess([
            'list'  => $listObj['data'],
            'count' => $listObj['total'],
        ]);
    }

    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminAuthGroup::update([
            'id'     => $id,
            'status' => $status,
        ]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function add() {
        $rules = [];
        $postData = $this->request->post();
        if($postData['rules']) {
            $rules = $postData['rules'];
            $rules = array_filter($rules);
        }
        unset($postData['rules']);
        $res = AdminAuthGroup::create($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }
        if($rules) {
            $insertData = [];
            foreach($rules as $value) {
                if($value) {
                    $insertData[] = [
                        'group_id' => $res->id,
                        'url'      => $value,
                    ];
                }
            }
            (new AdminAuthRule())->saveAll($insertData);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        if($postData['rules']) {
            $this->editRule();
        }
        unset($postData['rules']);
        $res = AdminAuthGroup::update($postData);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function del() {
        $id = $this->request->get('id');
        if(!$id) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $listInfo = (new AdminAuthGroupAccess())->where('find_in_set("' . $id . '", `group_id`)')->select();
        foreach($listInfo as $value) {
            $oldGroupArr = explode(',', $value->group_id);
            $key = array_search($id, $oldGroupArr);
            unset($oldGroupArr[$key]);
            $newData = implode(',', $oldGroupArr);
            $value->group_id = $newData;
            $value->save();
        }
        AdminAuthGroup::destroy($id);
        AdminAuthRule::destroy(['group_id' => $id]);

        return $this->buildSuccess([]);
    }

    public function delMember() {
        $gid = $this->request->get('gid', 0);
        $uid = $this->request->get('uid', 0);
        if(!$gid || !$uid) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $oldInfo = AdminAuthGroupAccess::get(['uid' => $uid])->toArray();
        $oldGroupArr = explode(',', $oldInfo['group_id']);
        $key = array_search($gid, $oldGroupArr);
        unset($oldGroupArr[$key]);
        $newData = implode(',', $oldGroupArr);
        $res = AdminAuthGroupAccess::update(['group_id' => $newData], ['uid' => $uid]);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function getGroups() {
        $listInfo = (new AdminAuthGroup())->where(['status' => Enum::isTrue])->order('id', 'DESC')->select()->toArray();
        $count = count($listInfo);

        return $this->buildSuccess([
            'list'  => $listInfo,
            'count' => $count,
        ]);
    }

    public function getRuleList() {
        $groupId = $this->request->get('group_id', 0);
        $list = (new AdminMenu)->order('sort', 'ASC')->select()->toArray();
        $list = Tools::listToTree($list);
        $rules = [];
        if($groupId) {
            $rules = (new AdminAuthRule())->where(['group_id' => $groupId])->select()->toArray();
            $rules = array_column($rules, 'url');
        }
        $newList = $this->buildList($list, $rules);

        return $this->buildSuccess([
            'list' => $newList,
        ]);
    }

    private function buildList($list, $rules) {
        $newList = [];
        foreach($list as $key => $value) {
            $newList[$key]['title'] = $value['name'];
            $newList[$key]['key'] = $value['url'];
            if(isset($value['_child'])) {
                $newList[$key]['expand'] = true;
                $newList[$key]['children'] = $this->buildList($value['_child'], $rules);
            } else {
                if(in_array($value['url'], $rules)) {
                    $newList[$key]['checked'] = true;
                }
            }
        }

        return $newList;
    }

    private function editRule() {
        $postData = $this->request->post();
        $needAdd = [];
        $has = (new AdminAuthRule())->where(['group_id' => $postData['id']])->select()->toArray();
        $hasRule = array_column($has, 'url');
        $needDel = array_flip($hasRule);
        foreach($postData['rules'] as $key => $value) {
            if(!empty($value)) {
                if(!in_array($value, $hasRule)) {
                    $data['url'] = $value;
                    $data['group_id'] = $postData['id'];
                    $needAdd[] = $data;
                } else {
                    unset($needDel[$value]);
                }
            }
        }
        if(count($needAdd)) {
            (new AdminAuthRule())->saveAll($needAdd);
        }
        if(count($needDel)) {
            $urlArr = array_keys($needDel);
            (new AdminAuthRule())->whereIn('url', $urlArr)->where('group_id', $postData['id'])->delete();
        }
    }
}
