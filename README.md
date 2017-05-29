# Odango

[![Build Status](https://travis-ci.org/LapazPhp/Odango.svg?branch=master)](https://travis-ci.org/LapazPhp/Odango)

Odango is a function compositor inspired by Aspect Oriented Programming (AOP).

To separate concerns is better practice even if you don't know AOP, for example
caching, logging, transaction, security filter, event dispatching or such as.
Cross cutting concerns should be separated from your business logic.

This is not an AOP weaving framework. I prefer Ray.Aop instead if you want some
full featured AOP like a Google Guice or such as.

## Example

```php
$withLoggedTransaction = AdviceComposite::of(function ($invocation) use ($logger) {
    $logger->info('Starting transaction.');
    $result = $invocation->proceed();
    $logger->info('Transaction comitted.');
    return $result;
})->with(function ($invocation) use ($db) {
    $db->beginTransaction();
    try {
        $result = $invocation->proceed();
        $db->commit();
        return $result;
    } catch (\Exception $ex) {
        $dbh->rollBack();
        throw $ex;
    }
});

$storeDataInvocation = [$this, 'storeData']; // Some callable
$storeDataInvocation = $withLoggedTransaction->bind($storeDataInvocation);

$storeDataInvocation($data);
```

Odango supports Ray.Aop's MethodInterceptor as composition target.
So, existing AOP assets may be reusable.

Remark that AdviceComposite instance is immutable because generated function
references the creation context. Modification breaks it.

## Known issue

`MethodInvocation::getThis()` and `Joinpoint::getThis()` are returns `null` because
no object context there are.
