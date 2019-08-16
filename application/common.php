<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

function p() {
    $argc = func_get_args();
    echo '<pre>';
    foreach($argc as $var) {
        print_r($var);
        echo '<br/>';
    }
    echo '</pre>';
    exit;

    return;
}

function pr() {
    $argc = func_get_args();
    echo '<pre>';
    foreach($argc as $var) {
        print_r($var);
        echo '<br/>';
    }
    echo '</pre>';
}
