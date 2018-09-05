<?php
namespace Lapaz\Odango;

use Ray\Aop\MethodInvocation;
use Ray\Aop\ReflectionMethod;

class CallbackInvocation implements MethodInvocation
{
    /**
     * @var object|array|string
     */
    protected $callback;

    /**
     * @var \ArrayObject
     */
    protected $arguments;

    /**
     * Invocation constructor.
     * @param callable $callback
     * @param array $arguments
     */
    public function __construct(callable $callback, array $arguments)
    {
        $this->callback = $callback;
        $this->arguments = new \ArrayObject($arguments);
    }

    /**
     * @inheritDoc
     */
    public function getThis()
    {
        throw new \BadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function getMethod() : ReflectionMethod
    {
        throw new \BadMethodCallException();
    }

    /**
     * @inheritDoc
     */
    public function getArguments() : \ArrayObject
    {
        return $this->arguments;
    }

    /**
     * @inheritDoc
     */
    public function getNamedArguments(): \ArrayObject
    {
        try {
            if (is_object($this->callback)) {
                $object = new \ReflectionObject($this->callback);
                $function = $object->getMethod('__invoke');
            } elseif (is_array($this->callback)) {
                $object = new \ReflectionObject($this->callback[0]);
                $function = $object->getMethod($this->callback[1]);
            } elseif (is_string($this->callback)) {
                $function = new \ReflectionFunction($this->callback);
            } else {
                throw new \UnexpectedValueException();
            }
        } catch (\ReflectionException $e) {
            throw new \UnexpectedValueException();
        }

        /** @var \ReflectionFunctionAbstract $function */
        $params = $function->getParameters();

        $namedParams = new \ArrayObject;
        foreach ($params as $param) {
            if (isset($this->arguments[$param->getPosition()])) {
                $namedParams[$param->getName()] = $this->arguments[$param->getPosition()];
            }
        }

        return $namedParams;
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
