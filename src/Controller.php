<?php
/**
 * Created by PhpStorm.
 * User: awei.tian
 * Date: 9/23/17
 * Time: 9:54 PM
 */
namespace Tian\Route;
use \Tian\Container;
class Controller
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}