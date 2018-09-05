<?php
namespace Lapaz\Odango;

use PHPUnit\Framework\TestCase;

class CallbackInvocationTest extends TestCase
{
    public function testConstruct()
    {
        $invocation = new CallbackInvocation(function ($a0, $a1) {
            return $a0 + $a1;
        }, [1, 2]);

        $this->assertEquals([1, 2], $invocation->getArguments()->getArrayCopy());
        $this->assertEquals(['a0' => 1, 'a1' => 2], $invocation->getNamedArguments()->getArrayCopy());
    }

    public function testProceed()
    {
        $invocation = new CallbackInvocation(function ($a0, $a1) {
            return $a0 + $a1;
        }, [1, 2]);

        $this->assertEquals(3, $invocation->proceed());
    }

    public function testNamedArgumentsFromInvokableObject()
    {
        $invocation = new CallbackInvocation(new class() {
            public function __invoke($a0, $a1)
            {
                return $a0 + $a1;
            }
        }, [1, 2]);

        $this->assertEquals(['a0' => 1, 'a1' => 2], $invocation->getNamedArguments()->getArrayCopy());
    }

    public function testNamedArgumentsFromMethodCallback()
    {
        $invocation = new CallbackInvocation([new class() {
            public function foo($a0, $a1)
            {
                return $a0 + $a1;
            }
        }, 'foo'], [1, 2]);

        $this->assertEquals(['a0' => 1, 'a1' => 2], $invocation->getNamedArguments()->getArrayCopy());
    }

    public function testNamedArgumentsFromGlobalFunction()
    {
        $invocation = new CallbackInvocation('range', [1, 10]);

        $this->assertEquals(['low' => 1, 'high' => 10], $invocation->getNamedArguments()->getArrayCopy());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testThisUnsupported()
    {
        $invocation = new CallbackInvocation(function () {
            return 0;
        }, []);

        $invocation->getThis();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testMethodUnsupported()
    {
        $invocation = new CallbackInvocation(function () {
            return 0;
        }, []);

        $invocation->getMethod();
    }
}
