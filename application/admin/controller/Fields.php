<?php

namespace app\admin\controller;

use app\model\AdminFields;
use app\model\AdminList;
use app\util\DataType;
use app\util\Enum;
use app\util\ReturnCode;

class Fields extends Base {

    public function request() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $hash = $this->request->get('hash', '');
        if(empty($hash)) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $listObj = (new AdminFields())->where(['hash' => $hash, 'type' => Enum::isFalse])
            ->paginate($limit, false, ['page' => $start])->toArray();
        $apiInfo = AdminList::get(['hash' => $hash]);

        return $this->buildSuccess([
            'list'     => $listObj['data'],
            'count'    => $listObj['total'],
            'dataType' => DataType::ALL_TYPE,
            'apiInfo'  => $apiInfo,
        ]);
    }

    public function response() {
        $limit = $this->request->get('size', config('apiadmin.admin_list_default'));
        $start = $this->request->get('page', 1);
        $hash = $this->request->get('hash', '');
        if(empty($hash)) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        $listObj = (new AdminFields())->where(['hash' => $hash, 'type' => 1])
            ->paginate($limit, false, ['page' => $start])->toArray();
        $apiInfo = AdminList::get(['hash' => $hash]);

        return $this->buildSuccess([
            'list'     => $listObj['data'],
            'count'    => $listObj['total'],
            'dataType' => DataType::ALL_TYPE,
            'apiInfo'  => $apiInfo,
        ]);
    }

    public function upload() {
        $hash = $this->request->post('hash');
        $type = $this->request->post('type');
        $jsonStr = $this->request->post('jsonStr');
        $jsonStr = html_entity_decode($jsonStr);
        $data = json_decode($jsonStr, true);
        if($data === null) {
            return $this->buildFailed(ReturnCode::EXCEPTION, 'JSON数据格式有误');
        }
        AdminList::update(['return_str' => json_encode($data)], ['hash' => $hash]);
        $this->handle($data['data'], $dataArr);
        $old = AdminFields::all([
            'hash' => $hash,
            'type' => $type,
        ])->toArray();
        $oldArr = array_column($old, 'show_name');
        $newArr = array_column($dataArr, 'show_name');
        $addArr = array_diff($newArr, $oldArr);
        $delArr = array_diff($oldArr, $newArr);
        if($delArr) {
            AdminFields::destroy(['show_name' => ['in', $delArr]]);
        }
        if($addArr) {
            $addData = [];
            foreach($dataArr as $item) {
                if(in_array($item['show_name'], $addArr)) {
                    $addData[] = $item;
                }
            }
            (new AdminFields())->insertAll($addData);
        }
        cache('RequestFields:NewRule:' . $hash, null);
        cache('RequestFields:Rule:' . $hash, null);
        cache('ResponseFieldsRule:' . $hash, null);

        return $this->buildSuccess();
    }

    public function add() {
        $postData = $this->request->post();
        $postData['show_name'] = $postData['field_name'];
        $postData['default'] = $postData['defaults'];
        unset($postData['defaults']);
        $res = AdminFields::create($postData);
        cache('RequestFields:NewRule:' . $postData['hash'], null);
        cache('RequestFields:Rule:' . $postData['hash'], null);
        cache('ResponseFieldsRule:' . $postData['hash'], null);
        if($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR);
        }

        return $this->buildSuccess();
    }

    public function edit() {
        $postData = $this->request->post();
        $postData['show_name'] = $postData['field_name'];
        $postData['default'] = $postData['defaults'];
        unset($postData['defaults']);
        $res = AdminFields::update($postData);
        cache('RequestFields:NewRule:' . $postData['hash'], null);
        cache('RequestFields:Rule:' . $postData['hash'], null);
        cache('ResponseFieldsRule:' . $postData['hash'], null);
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
        $fieldsInfo = AdminFields::get($id);
        cache('RequestFields:NewRule:' . $fieldsInfo->hash, null);
        cache('RequestFields:Rule:' . $fieldsInfo->hash, null);
        cache('ResponseFieldsRule:' . $fieldsInfo->hash, null);
        AdminFields::destroy($id);

        return $this->buildSuccess();
    }

    private function handle($data, &$dataArr, $prefix = 'data', $index = 'data') {
        if(!$this->isAssoc($data)) {
            $addArr = [
                'field_name' => $index,
                'show_name'  => $prefix,
                'hash'       => $this->request->post('hash'),
                'is_must'    => 1,
                'data_type'  => DataType::TYPE_ARRAY,
                'type'       => $this->request->post('type'),
            ];
            $dataArr[] = $addArr;
            $prefix .= '[]';
            if(isset($data[0]) && is_array($data[0])) {
                $this->handle($data[0], $dataArr, $prefix);
            }
        } else {
            $addArr = [
                'field_name' => $index,
                'show_name'  => $prefix,
                'hash'       => $this->request->post('hash'),
                'is_must'    => 1,
                'data_type'  => DataType::TYPE_OBJECT,
                'type'       => $this->request->post('type'),
            ];
            $dataArr[] = $addArr;
            $prefix .= '{}';
            foreach($data as $index => $datum) {
                $myPre = $prefix . $index;
                $addArr = [
                    'field_name' => $index,
                    'show_name'  => $myPre,
                    'hash'       => $this->request->post('hash'),
                    'is_must'    => 1,
                    'data_type'  => DataType::TYPE_STRING,
                    'type'       => $this->request->post('type'),
                ];
                if(is_numeric($datum)) {
                    if(preg_match('/^\d*$/', $datum)) {
                        $addArr['data_type'] = DataType::TYPE_INTEGER;
                    } else {
                        $addArr['data_type'] = DataType::TYPE_FLOAT;
                    }
                    $dataArr[] = $addArr;
                } elseif(is_array($datum)) {
                    $this->handle($datum, $dataArr, $myPre, $index);
                } else {
                    $addArr['data_type'] = DataType::TYPE_STRING;
                    $dataArr[] = $addArr;
                }
            }
        }
    }

    private function isAssoc(array $arr) {
        if(array() === $arr) return false;

        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
