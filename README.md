# 信呼文件管理平台，免费开源的文件管理平台


开发者：雨中磐石(rainrock)  
邮箱：admin@rockoa.com  
公司团队：信呼开发团队  
官网网站：http://www.rockoa.com/  
源码仅供学习二次开发使用，禁止用于商业用途出售等(违者必究)  
版权：Copyright @2016-2019 信呼开发团队  
版本整理时间：2019-05-28  
版本号：V1.0.0  

### 安装方式  
```php
composer install
php -r "file_exists('.env') or copy('.env.example', '.env');" //创建配置文件并修改里面配置参数，如数据库信息
php artisan rock:docs checkbase //检测数据库是否存在，不存在就创建
php artisan migrate //导入系统表
php artisan db:seed //创建后台管理员
```

### 项目介绍 
1、项目基于Laravel5.4版本开发的。  
2、详细介绍：http://www.rockoa.com/view_rockdoc.html  