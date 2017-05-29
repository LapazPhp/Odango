<?php
namespace Lapaz\Odango;

use PHPUnit\Framework\TestCase;
use Ray\Aop\MethodInvocation;

class CallbackInterceptorTest extends TestCase
{

    public function testInvoke()
    {
        $invocation = $this->createMock(MethodInvocation::class);
        $invocation->method('proceed')->willReturn(1);

        $interceptor = new CallbackInterceptor(function (MethodInvocation $invocation) {
            return $invocation->proceed() + 10;
        });

        $this->assertEquals(11, $interceptor->invoke($invocation));
    }
}
