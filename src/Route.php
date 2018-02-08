<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/25
 * Time: 11:01
 * Route 是一条路由记录
 *      一条路由记录由3个属性组成
 *      - 匹配URL参数,把这个参数用UrlMatcher匹配
 *      - 中间件,匹配成功后需要经过的管道
 *      - 通过管道后执行的业务函数
 */

namespace Aw\Routing;

use Aw\Http\Request;
use Aw\Pipeline;


class Route
{

    /**
     * method = 'post|get|put'
     * host = 127.0.0.1|localhost
     * type = equal|startwith|regexp|mapca-pmcai|mapca-pmi|mapca-arr|callback
     * data = mxied
     * @var array
     */
    protected $matches = array();         //用于匹配的数据
    protected $middleware = array();    //匹配成功需要通过的中间件
    protected $action;        //通过中间件后执行的业务逻辑
    /**
     * @var Request
     */
    protected $request;

    /**
     * @return bool
     */
    public function match()
    {
        $matcher = new Matcher($this->request, $this->matches);
        return $matcher->match();
    }

    /**
     * @return mixed
     */
    public function route()
    {
        $mw = new Middleware($this->middleware);
        $dp = new Dispatcher($this->action);
        $pipe = new Pipeline();
        return $pipe->send($this->request)
            ->through($mw->getMiddleware())
            ->then($dp->getAction());
    }

    /**
     * @return array
     */
    public function getMatch()
    {
        return $this->matches;
    }

    /**
     * @param array $match
     */
    public function setMatch($match)
    {
        $this->matches = $match;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param mixed $middleware
     */
    public function setMiddleware($middleware)
    {
        $this->middleware = $middleware;
    }


    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }
}