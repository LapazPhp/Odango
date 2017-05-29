<?php
namespace Lapaz\Odango;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class CallbackInterceptor implements MethodInterceptor
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * CallbackInterceptor constructor.
     * @param callable $callback
     */
    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param MethodInvocation $invocation
     * @return mixed
     */
    public function invoke(MethodInvocation $invocation)
    {
        return call_user_func($this->callback, $invocation);
    }
}
