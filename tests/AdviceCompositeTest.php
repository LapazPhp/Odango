<?php
namespace Lapaz\Odango;

use PHPUnit\Framework\TestCase;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class AdviceCompositeTest extends TestCase
{
    public function testCallbackCompositeToAdvice()
    {
        $addOneAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 1;
        };

        $addTwoAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 2;
        };

        $composite = AdviceComposite::of($addTwoAdvice)->with($addOneAdvice);

        $mockInvocation = $this->createMock(MethodInvocation::class);
        $mockInvocation->method('proceed')->willReturn(1);

        $compositeAdvice = $composite->toAdvice();
        $this->assertEquals(4, $compositeAdvice->invoke($mockInvocation));


        $sumFunction = function ($numbers) {
            return array_sum($numbers);
        };

        $sumFunctionAdvised = $composite->bind($sumFunction);

        $this->assertEquals(9, $sumFunctionAdvised([1, 2, 3]));
    }

    public function testInterceptorCompositeToAdvice()
    {
        $addOneAdvice = $this->createMock(MethodInterceptor::class);
        $addOneAdvice->method('invoke')->willReturnCallback(function (MethodInvocation $invocation) {
            return $invocation->proceed() + 1;
        });

        $addTwoAdvice = $this->createMock(MethodInterceptor::class);
        $addTwoAdvice->method('invoke')->willReturnCallback(function (MethodInvocation $invocation) {
            return $invocation->proceed() + 2;
        });

        $composite = AdviceComposite::of($addTwoAdvice)->with($addOneAdvice);

        $mockInvocation = $this->createMock(MethodInvocation::class);
        $mockInvocation->method('proceed')->willReturn(1);

        $compositeAdvice = $composite->toAdvice();
        $this->assertEquals(4, $compositeAdvice->invoke($mockInvocation));
    }

    public function testCompositeToDecorator()
    {
        $addOneAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 1;
        };

        $addTwoAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 2;
        };

        $decorator = AdviceComposite::of($addTwoAdvice)->with($addOneAdvice)->toDecorator();
        $sumFunction = function ($numbers) {
            return array_sum($numbers);
        };

        $sumFunctionAdvised = $decorator($sumFunction);
        $this->assertEquals(9, $sumFunctionAdvised([1, 2, 3]));
    }

    public function testCompositeBinding()
    {
        $addOneAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 1;
        };

        $addTwoAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 2;
        };

        $composite = AdviceComposite::of($addTwoAdvice)->with($addOneAdvice);

        $sumFunction = function ($numbers) {
            return array_sum($numbers);
        };
        $sumFunctionAdvised = $composite->bind($sumFunction);

        $this->assertEquals(9, $sumFunctionAdvised([1, 2, 3]));
    }

    public function testCompositeInvocation()
    {
        $addOneAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 1;
        };

        $addTwoAdvice = function (MethodInvocation $invocation) {
            return $invocation->proceed() + 2;
        };

        $composite = AdviceComposite::of($addTwoAdvice)->with($addOneAdvice);

        $sumFunction = function ($numbers) {
            return array_sum($numbers);
        };

        $this->assertEquals(9, $composite->invoke($sumFunction, [1, 2, 3]));
    }
}
