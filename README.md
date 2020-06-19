# php-thinkphp-api-admin

## 特别提示

> 本项目前后端分离，与[vue-iview-api-admin](https://github.com/bstdn/vue-iview-api-admin)或[vue-element-api-admin](https://github.com/bstdn/vue-element-api-admin)配合使用

## 环境需求

- PHP >= 5.6
- MySQL >= 5.5.3
- Redis

## 安装

### 克隆代码

```
git clone https://github.com/bstdn/php-thinkphp-api-admin.git
cd php-thinkphp-api-admin
composer install
```

### 检测环境以及配置数据库

```
php think apiadmin:install -h
#Options:
#      --db=DB            数据库连接参数，格式为：数据库类型://用户名:密码@数据库地址:数据库端口/数据库名#字符集
#      --prefix[=PREFIX]  数据库表前缀 [default: ""]

# 示例:
php think apiadmin:install --db mysql://root:123456@127.0.0.1:3306/apiadmin#utf8mb4
php think apiadmin:install --db mysql://root:123456@127.0.0.1:3306/apiadmin#utf8mb4 --prefix=api_
```

### 数据库迁移

```
php think migrate:run

# 安装完成后，查看后台管理员的账号密码
cat application/install/lock.ini
```

### Redis配置

```
# 修改 Redis 相关配置；如不使用 Redis，可修改为文件缓存；详细可查看：https://www.kancloud.cn/manual/thinkphp5_1/354116
config/cache.php
```

## 相关技术栈

- [ThinkPHP](https://github.com/top-think/think)

## 效果展示

- vue-iview-api-admin

![输入图片说明](https://images.gitee.com/uploads/images/2019/1123/111717_e6b82bff_1185106.png "menu.png")

![输入图片说明](https://images.gitee.com/uploads/images/2019/1123/111822_99d94720_1185106.png "appslist.png")

![输入图片说明](https://images.gitee.com/uploads/images/2019/1123/111842_1845ceb0_1185106.png "interfaceList.png")

- vue-element-api-admin

![输入图片说明](https://images.gitee.com/uploads/images/2019/1123/111129_50384013_1185106.png "menu.png")

![输入图片说明](https://images.gitee.com/uploads/images/2019/1123/111426_7130df97_1185106.png "appsList.png")

![输入图片说明](https://images.gitee.com/uploads/images/2019/1123/111451_2df8d595_1185106.png "interfaceList.png")

## 赞赏

**请作者喝杯咖啡吧！(微信号/QQ号：99808359)**

<img width="236" alt="微信扫一扫" src="https://images.gitee.com/uploads/images/2019/1122/203838_862f04ff_1185106.jpeg">

## License

[MIT](https://github.com/bstdn/php-thinkphp-api-admin/blob/master/LICENSE)

Copyright (c) 2019-present, bstdn
