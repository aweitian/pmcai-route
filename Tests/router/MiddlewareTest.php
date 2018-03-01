<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace {

    use Aw\Http\Request;
    use Aw\Routing\Dispatch\AtCall;
    use Aw\Routing\Router\Middleware;
    use Aw\Routing\Router\Router;

    class MiddlewareTest extends \PHPUnit_Framework_TestCase
    {
        public function testRun()
        {
            $defined_mw = array(
                'd1' => 'defined1',
                'd2' => 'df2'
            );
            $global_mw = array(
                'global-mw',
                'gmw2'
            );
            //global|defined|private|use_global
            $mw = new Middleware(array(
                'global' => $global_mw,
                'defined' => $defined_mw,
                'private' => array(
                    'pv1', 'd2'
                ),
                true
            ));
            $this->assertArraySubset(array(
                "global-mw", "gmw2", "pv1", "df2"
            ), $mw->getMiddleware());

            $this->assertArraySubset(array(
                "global-mw", "gmw2"
            ), $mw->getMiddleware(array(), true));

            $this->assertTrue(empty($mw->getMiddleware(array(), false)));
        }
    }
}

