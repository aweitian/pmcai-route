# 路由第三版

![Image text](https://raw.githubusercontent.com/aweitian/route/master/md.png)


## 安装组件
使用 composer 命令进行安装或下载源代码使用。
> composer require aweitian/route
>

## 匹配
 - 函数
 - 相等
 - 请求方式
 - 正则
 - 组合
 
## 派遣
 - 命名空间\类@方法
 - 回调函数

## 路由
 - 命名空间\类@方法
 - 回调函数
 
## 路由器
 - ca($middleware = array(), $action = "\\App\\Http\\Default\\(:1)@(:2)")
 - mca($middleware = array(), $action = "\\App\\Http\\(:1)\\(:2)@(:3)")
 - get($url, $action, $middleware = array()) 见下面补充说明
 - post
 - delete
 - put
 - any($url, $action, $middleware = array(), $method = "*") method可以为数组
 - match 正则
 - add404Handler 404回调
 - addRegexpPlaceholder 添加更多正则占位符
    
## 路由器URL/action参数
 - url 以#开头和结尾 或者 路径中包包含  :num :alpha :var 用正则匹配 
 - action 支持callback 或者  \namespace\class@method
 - callback of action 第一个参数是request,第二个参数是matches
 - atCall of action 第一个参数是matches ,类构造函数的参数是request
    
