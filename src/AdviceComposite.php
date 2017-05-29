<?php
namespace Lapaz\Odango;

use Ray\Aop\Advice;
use Ray\Aop\Arguments;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

class AdviceComposite
{
    /**
     * @var callable[]
     */
    protected $interceptors = [];

    /**
     * @var mixed
     */
    protected $context = null;

    /**
     * @param Advice|callable $advice
     * @return static
     */
    public static function of($advice)
    {
        $compositor = new static();
        return $compositor->with($advice);
    }

    /**
     * @param Advice|callable $advice
     * @return static
     */
    public function with($advice)
    {
        if ($advice instanceof Advice) {
            if ($advice instanceof MethodInterceptor || $advice instanceof CallbackInterceptor) {
            } else {
                throw new \InvalidArgumentException('Unsupported advice type: ' . get_class($advice));
            }
        } elseif (is_callable($advice)) {
            $advice = new CallbackInterceptor($advice);
        } elseif (is_object($advice)) {
            throw new \InvalidArgumentException('Unsupported advice type: ' . get_class($advice));
        } else {
            throw new \InvalidArgumentException('Unsupported advice value: ' . strval($advice));
        }

        $that = clone $this;
        $that->interceptors[] = $advice;
        return $that;
    }

    /**
     * @return MethodInterceptor
     */
    public function toAdvice()
    {
        return new CallbackInterceptor(function (MethodInvocation $invocation) {
            // before
            return $this->consumeInterceptors($this->interceptors, $invocation);
            // after return/trow
        });
    }

    /**
     * @param MethodInterceptor[] $interceptors
     * @param MethodInvocation $invocation
     * @return mixed
     */
    protected function consumeInterceptors(array $interceptors, MethodInvocation $invocation)
    {
        if (empty($interceptors)) {
            return $invocation->proceed();
        }

        $headInterceptor = $interceptors[0];
        $tailInterceptors = array_slice($interceptors, 1);

        assert($headInterceptor instanceof MethodInterceptor);

        $nextInvocation = new CallbackInvocation(function () use ($tailInterceptors, $invocation) {
            return $this->consumeInterceptors($tailInterceptors, $invocation);
        }, new Arguments());

        return $headInterceptor->invoke($nextInvocation);
    }

    /**
     * @return callable
     */
    public function toDecorator()
    {
        return function ($target) {
            return function (...$arguments) use ($target) {
                $arguments = new Arguments($arguments);
                $invocation = new CallbackInvocation($target, $arguments);
                return $this->toAdvice()->invoke($invocation);
            };
        };
    }

    /**
     * @param callable $target
     * @return callable
     */
    public function bind(callable $target)
    {
        $decorator = $this->toDecorator();
        return $decorator($target);
    }

    /**
     * @param callable $target
     * @param array ...$arguments
     * @return mixed
     */
    public function invoke(callable $target, ...$arguments)
    {
        return call_user_func_array($this->bind($target), $arguments);
    }
}
