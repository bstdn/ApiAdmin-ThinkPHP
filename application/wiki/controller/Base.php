<?php

namespace app\wiki\controller;

use app\util\ReturnCode;
use think\Controller;

class Base extends Controller {

    protected $appInfo;

    public function __construct() {
        parent::__construct();
        $this->appInfo = $this->request->API_WIKI_USER_INFO;
    }

    public function buildSuccess($data = [], $msg = '操作成功', $code = ReturnCode::SUCCESS) {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        return $result;
    }

    public function buildFailed($code, $msg = '操作失败', $data = []) {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data,
        ];

        return $result;
    }
}
