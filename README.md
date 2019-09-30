# 路由第三版

![Image text](https://raw.githubusercontent.com/aweitian/route/master/readme.png)


## 安装组件
使用 composer 命令进行安装或下载源代码使用。
> composer require aweitian/route
>

### 匹配URL路径 match
- ca 返回 array(control,action)
- mca 返回 array(module,control,action)
- regexp 返回 matches
- equal 返回 array()
- startWith array(endsWith)

### 映射 map 
> ca 第一个 c  第二个 a
> mca 第一个m  第二个 c  第三个 a
> regexp  根据mask的个数,二个是ca 三个是mca
- map($result,$class_pattern='{c}',$action_pattern='{a}',$namespace_pattern='App\Modules\{m}',$c_default='main',$a_default='index',$m_default='Controller')

### 派遣 dispatch
- 函数
- 方法
    - namespace
    - class
    - method
### 路由器
- 路由到函数,直接把数据作为参数(一个参数)调用函数
- 路由到方法
    - route($result) 
    - 
    
        
    
