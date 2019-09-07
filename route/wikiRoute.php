<?php

use think\facade\Route;

Route::group('wiki', function() {
    Route::rule('Api/login', 'wiki/Api/login', 'post');
    Route::group('Api', [
        'groupList' => ['wiki/Api/groupList', ['method' => 'get']],
        'errorCode' => ['wiki/Api/errorCode', ['method' => 'get']],
        'detail'    => ['wiki/Api/detail', ['method' => 'get']],
        'logout'    => ['wiki/Api/logout', ['method' => 'get']],
    ])->middleware(['WikiAuth']);
    //MISS路由定义
    Route::miss('admin/Miss/index');
})->middleware('AdminResponse');
