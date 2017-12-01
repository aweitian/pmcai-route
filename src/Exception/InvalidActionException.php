<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tian\Route\Exception;


/**
 * Exception thrown when a route does not exist.
 *
 * @author Alexandre Salomé <alexandre.salome@gmail.com>
 */
class InvalidActionException extends \InvalidArgumentException implements ExceptionInterface
{
    protected $className;
    public function __construct($message = "InvalidActionException", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    public function setClass($class)
    {
        $this->className = $class;
    }
    public function getClass()
    {
        return $this->className;
    }
}
