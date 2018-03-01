<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class RegexpTest extends \PHPUnit_Framework_TestCase
{

    public function testRegexp()
    {
        $matcher = new \Aw\Routing\Matcher\Regexp(array(
            'regexp' => "#^/$#"
        ));
        $ret = $matcher->match(new \Aw\Http\Request("/"));
        $this->assertTrue($ret);

        $matcher = new \Aw\Routing\Matcher\Regexp(array(
            'regexp' => "#^/prefix/\d+/aa$#"
        ));
        $this->assertTrue($matcher->match(new \Aw\Http\Request("/prefix/123/aa")));
        $this->assertFalse($matcher->match(new \Aw\Http\Request("/prefix/aas/aa")));
    }

}
