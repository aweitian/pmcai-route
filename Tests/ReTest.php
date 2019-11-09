<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class ReTest extends \PHPUnit_Framework_TestCase
{
    public function testMatch()
    {
        $callback = function ($a, $b = true) {
            return $a + 1;
        };

        $re = new ReflectionFunction($callback);
        if ($re->getNumberOfParameters() == 2) {
            $new_c = function ($a, $b, $c) use ($callback) {

                $d = 1;
                return $callback($d, $a);
            };
            $this->assertEquals(2, $new_c(1, 1, 1));
        }
    }
}
