<?php
namespace Lapaz\Odango;

use Ray\Aop\Arguments;
use Ray\Aop\MethodInvocation;

class CallbackInvocation implements MethodInvocation
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var Arguments
     */
    protected $arguments;

    /**
     * Invocation constructor.
     * @param callable $callback
     * @param Arguments $arguments
     */
    public function __construct(callable $callback, Arguments $arguments)
    {
        $this->callback = $callback;
        $this->arguments = $arguments;
    }

    /**
     * @inheritDoc
     */
    public function getThis()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getMethod()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function proceed()
    {
        $args = $this->arguments->getArrayCopy();
        return call_user_func_array($this->callback, $args);
    }
}
