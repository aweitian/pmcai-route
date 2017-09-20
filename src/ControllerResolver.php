<?php
/**
 * Created by PhpStorm.
 * User: awei.tian
 * Date: 9/20/17
 * Time: 8:03 PM
 */

namespace Tian\Route;
use \Tian\Http\Request;
use \Tian\Http\Response;
use \Tian\Route\Route;

class ControllerResolver
{

    /**
     * @param Request $request
     * @param Route $route
     * @return Response
     */
    public function resolver(Request $request,Route $route)
    {
//        var_dump($request->attributes->all());
        $call = $route->getOption("_call");
        if (array_key_exists("uses",$call))
        {

        }
        elseif (is_callable($call[0]))
        {
            return new Response($call[0]());
        }
        return new Response();
    }
}