# 常用路由匹配
## 安装组件
使用 composer 命令进行安装或下载源代码使用。
>composer require aweitian/route
>
## 三个中间件变量的作用
* middlewareGroups  用于索引,取定义作用
* middleware 用于索引,取定义作用
* middlewarePriority 全局路由生效 

> 单独使用passMiddlewarePriority使middlewarePriority对本路由不生效

> App("router.matched.action")来获取$router->get第二个参数

> App("router.matched.route")来获取匹配的route
$router->get('/{bar}/{lol}',function(){});

## 示例
<pre>
$router = new RouteCollection();
$router->setMiddleware([
    'test' => function(Request $request,$next) {
        $request->attributes->add([
            'test' => 'test - Middleware'
        ]);
        return $next($request);
    }
]);

$router->setMiddlewareGroups([
    'testGrp' => [
        function(Request $request,$next) {
            $request->attributes->add([
                'testGrp1' => 'testGrp1 - Middleware'
            ]);
            return $next($request);
        },
        function(Request $request,$next) {
            $request->attributes->add([
                'testGrp2' => 'testGrp2 - Middleware'
            ]);
            return $next($request);
        }
    ]
]);
//第二个参数数组说明
//第一个元素为callback
$router->get('/{bar}/{lol}',["\\Tian\\Route\\Tests\\Matcher\\classParameter@middleware",
    "middleware" => [
        function (Request $request,$next) {
            $request->attributes->add([
                'aa' => 'bb'
            ]);
            return $next($request);
        },
        "test",
        "testGrp"
    ]
]);
$response = $router->dispatch(Request::create("/abar/blol"));
</pre>
