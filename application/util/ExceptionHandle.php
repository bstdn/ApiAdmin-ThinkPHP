<?php

namespace app\util;

use Exception;
use think\exception\Handle;

class ExceptionHandle extends Handle {

    public function render(Exception $e) {
        return parent::render($e)->header(config('apiadmin.cross_domain'));
    }
}
