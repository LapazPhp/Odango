<?php
namespace Lapaz\Odango;

use PHPUnit\Framework\TestCase;
use Ray\Aop\Arguments;

class CallbackInvocationTest extends TestCase
{
    public function testConstruct()
    {
        $invocation = new CallbackInvocation(function ($a0, $a1) {
            return $a0 + $a1;
        }, new Arguments([1, 2]));

        $this->assertNull($invocation->getThis());
        $this->assertNull($invocation->getMethod());
        $this->assertEquals([1, 2], $invocation->getArguments()->getArrayCopy());
    }

    public function testProceed()
    {
        $invocation = new CallbackInvocation(function ($a0, $a1) {
            return $a0 + $a1;
        }, new Arguments([1, 2]));

        $this->assertEquals(3, $invocation->proceed());
    }
}
