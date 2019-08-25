# php-thinkphp-api-admin

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
php think apiadmin:install --db mysql://root:123456@127.0.0.1:3306/apiadmin#utf8
php think apiadmin:install --db mysql://root:123456@127.0.0.1:3306/apiadmin#utf8 --prefix=api_
```

### 数据库迁移

```
php think migrate:run

# 安装完成后，查看后台管理员的账号密码
cat application/install/lock.ini
```
