<?php

use think\facade\Route;

Route::group('wiki', function() {
    Route::rule('Api/login', 'wiki/Api/login', 'post');
    Route::group('Api', [
        'logout'    => ['wiki/Api/logout', ['method' => 'get']],
    ])->middleware(['WikiAuth']);
    //MISS路由定义
    Route::miss('admin/Miss/index');
})->middleware('AdminResponse');
