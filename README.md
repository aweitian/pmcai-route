# 路由第三版
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
> 如果长度大于等于3 存在 0 作为 {m} ,1 作为 {c},2作为{a}
> 如果长度小于3 , 存在0 作为 {c} ,存在1 作为 {a}
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
    
        
    
