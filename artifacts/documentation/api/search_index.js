var search_data = {
    'index': {
        'searchIndex': ["icecave","icecave\\chastity","icecave\\chastity\\driver","icecave\\chastity\\driver\\redis","icecave\\chastity\\exception","icecave\\chastity\\driver\\blockingadaptor","icecave\\chastity\\driver\\blockingdriverinterface","icecave\\chastity\\driver\\driverinterface","icecave\\chastity\\driver\\redis\\redisdriver","icecave\\chastity\\exception\\lockacquisitionexception","icecave\\chastity\\exception\\lockalreadyacquiredexception","icecave\\chastity\\exception\\locknotacquiredexception","icecave\\chastity\\lock","icecave\\chastity\\lockfactory","icecave\\chastity\\lockfactoryinterface","icecave\\chastity\\lockinterface","icecave\\chastity\\packageinfo","icecave\\chastity\\reentrantlock","icecave\\chastity\\driver\\blockingadaptor::__construct","icecave\\chastity\\driver\\blockingadaptor::acquire","icecave\\chastity\\driver\\blockingadaptor::isacquired","icecave\\chastity\\driver\\blockingadaptor::extend","icecave\\chastity\\driver\\blockingadaptor::release","icecave\\chastity\\driver\\blockingdriverinterface::acquire","icecave\\chastity\\driver\\driverinterface::acquire","icecave\\chastity\\driver\\driverinterface::isacquired","icecave\\chastity\\driver\\driverinterface::extend","icecave\\chastity\\driver\\driverinterface::release","icecave\\chastity\\driver\\redis\\redisdriver::__construct","icecave\\chastity\\driver\\redis\\redisdriver::acquire","icecave\\chastity\\driver\\redis\\redisdriver::isacquired","icecave\\chastity\\driver\\redis\\redisdriver::extend","icecave\\chastity\\driver\\redis\\redisdriver::release","icecave\\chastity\\exception\\lockacquisitionexception::__construct","icecave\\chastity\\exception\\lockalreadyacquiredexception::__construct","icecave\\chastity\\exception\\locknotacquiredexception::__construct","icecave\\chastity\\lock::__construct","icecave\\chastity\\lock::__destruct","icecave\\chastity\\lock::resource","icecave\\chastity\\lock::isacquired","icecave\\chastity\\lock::acquire","icecave\\chastity\\lock::tryacquire","icecave\\chastity\\lock::extend","icecave\\chastity\\lock::release","icecave\\chastity\\lockfactory::__construct","icecave\\chastity\\lockfactory::defaultttl","icecave\\chastity\\lockfactory::setdefaultttl","icecave\\chastity\\lockfactory::create","icecave\\chastity\\lockfactory::acquire","icecave\\chastity\\lockfactory::tryacquire","icecave\\chastity\\lockfactoryinterface::create","icecave\\chastity\\lockfactoryinterface::defaultttl","icecave\\chastity\\lockfactoryinterface::setdefaultttl","icecave\\chastity\\lockfactoryinterface::acquire","icecave\\chastity\\lockfactoryinterface::tryacquire","icecave\\chastity\\lockinterface::__destruct","icecave\\chastity\\lockinterface::resource","icecave\\chastity\\lockinterface::isacquired","icecave\\chastity\\lockinterface::acquire","icecave\\chastity\\lockinterface::tryacquire","icecave\\chastity\\lockinterface::extend","icecave\\chastity\\lockinterface::release","icecave\\chastity\\reentrantlock::__construct","icecave\\chastity\\reentrantlock::__destruct","icecave\\chastity\\reentrantlock::resource","icecave\\chastity\\reentrantlock::isacquired","icecave\\chastity\\reentrantlock::acquire","icecave\\chastity\\reentrantlock::tryacquire","icecave\\chastity\\reentrantlock::extend","icecave\\chastity\\reentrantlock::release"],
        'info': [["Icecave","","Icecave.html","","",3],["Icecave\\Chastity","","Icecave\/Chastity.html","","",3],["Icecave\\Chastity\\Driver","","Icecave\/Chastity\/Driver.html","","",3],["Icecave\\Chastity\\Driver\\Redis","","Icecave\/Chastity\/Driver\/Redis.html","","",3],["Icecave\\Chastity\\Exception","","Icecave\/Chastity\/Exception.html","","",3],["BlockingAdaptor","Icecave\\Chastity\\Driver","Icecave\/Chastity\/Driver\/BlockingAdaptor.html","","Emulates blocking lock acquisition for non-blocking",1],["BlockingDriverInterface","Icecave\\Chastity\\Driver","Icecave\/Chastity\/Driver\/BlockingDriverInterface.html","","A low-level lock implementation that supports blocking",1],["DriverInterface","Icecave\\Chastity\\Driver","Icecave\/Chastity\/Driver\/DriverInterface.html","","A low-level lock implementation.",1],["RedisDriver","Icecave\\Chastity\\Driver\\Redis","Icecave\/Chastity\/Driver\/Redis\/RedisDriver.html","","",1],["LockAcquisitionException","Icecave\\Chastity\\Exception","Icecave\/Chastity\/Exception\/LockAcquisitionException.html"," < RuntimeException","",1],["LockAlreadyAcquiredException","Icecave\\Chastity\\Exception","Icecave\/Chastity\/Exception\/LockAlreadyAcquiredException.html"," < LogicException","",1],["LockNotAcquiredException","Icecave\\Chastity\\Exception","Icecave\/Chastity\/Exception\/LockNotAcquiredException.html"," < LogicException","",1],["Lock","Icecave\\Chastity","Icecave\/Chastity\/Lock.html","","",1],["LockFactory","Icecave\\Chastity","Icecave\/Chastity\/LockFactory.html","","",1],["LockFactoryInterface","Icecave\\Chastity","Icecave\/Chastity\/LockFactoryInterface.html","","",1],["LockInterface","Icecave\\Chastity","Icecave\/Chastity\/LockInterface.html","","",1],["PackageInfo","Icecave\\Chastity","Icecave\/Chastity\/PackageInfo.html","","",1],["ReentrantLock","Icecave\\Chastity","Icecave\/Chastity\/ReentrantLock.html","","Wraps an existing lock to provide reentrancy support.",1],["BlockingAdaptor::__construct","Icecave\\Chastity\\Driver\\BlockingAdaptor","Icecave\/Chastity\/Driver\/BlockingAdaptor.html#method___construct","(<a href=\"Icecave\/Chastity\/Driver\/DriverInterface.html\"><abbr title=\"Icecave\\Chastity\\Driver\\DriverInterface\">DriverInterface<\/abbr><\/a> $driver, <abbr title=\"Icecave\\Interlude\\InvokerInterface\">InvokerInterface<\/abbr> $invoker = null, $delay = 0.1)","",2],["BlockingAdaptor::acquire","Icecave\\Chastity\\Driver\\BlockingAdaptor","Icecave\/Chastity\/Driver\/BlockingAdaptor.html#method_acquire","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $timeout)","Acquire a lock on the given resource.",2],["BlockingAdaptor::isAcquired","Icecave\\Chastity\\Driver\\BlockingAdaptor","Icecave\/Chastity\/Driver\/BlockingAdaptor.html#method_isAcquired","(string $resource, string $token)","Check if the given token still represents an acquired",2],["BlockingAdaptor::extend","Icecave\\Chastity\\Driver\\BlockingAdaptor","Icecave\/Chastity\/Driver\/BlockingAdaptor.html#method_extend","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $ttl)","Extend the TTL of a lock that has already been acquired.",2],["BlockingAdaptor::release","Icecave\\Chastity\\Driver\\BlockingAdaptor","Icecave\/Chastity\/Driver\/BlockingAdaptor.html#method_release","(string $resource, string $token)","Release a lock.",2],["BlockingDriverInterface::acquire","Icecave\\Chastity\\Driver\\BlockingDriverInterface","Icecave\/Chastity\/Driver\/BlockingDriverInterface.html#method_acquire","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $timeout)","Acquire a lock on the given resource.",2],["DriverInterface::acquire","Icecave\\Chastity\\Driver\\DriverInterface","Icecave\/Chastity\/Driver\/DriverInterface.html#method_acquire","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $ttl)","Acquire a lock on the given resource.",2],["DriverInterface::isAcquired","Icecave\\Chastity\\Driver\\DriverInterface","Icecave\/Chastity\/Driver\/DriverInterface.html#method_isAcquired","(string $resource, string $token)","Check if the given token still represents an acquired",2],["DriverInterface::extend","Icecave\\Chastity\\Driver\\DriverInterface","Icecave\/Chastity\/Driver\/DriverInterface.html#method_extend","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\float\">float<\/abbr> $ttl)","Extend the TTL of a lock that has already been acquired.",2],["DriverInterface::release","Icecave\\Chastity\\Driver\\DriverInterface","Icecave\/Chastity\/Driver\/DriverInterface.html#method_release","(string $resource, string $token)","Release a lock.",2],["RedisDriver::__construct","Icecave\\Chastity\\Driver\\Redis\\RedisDriver","Icecave\/Chastity\/Driver\/Redis\/RedisDriver.html#method___construct","(<abbr title=\"Predis\\ClientInterface\">ClientInterface<\/abbr> $redisClient)","",2],["RedisDriver::acquire","Icecave\\Chastity\\Driver\\Redis\\RedisDriver","Icecave\/Chastity\/Driver\/Redis\/RedisDriver.html#method_acquire","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\Redis\\float\">float<\/abbr> $ttl)","Acquire a lock on the given resource.",2],["RedisDriver::isAcquired","Icecave\\Chastity\\Driver\\Redis\\RedisDriver","Icecave\/Chastity\/Driver\/Redis\/RedisDriver.html#method_isAcquired","(string $resource, string $token)","Check if the given token still represents an acquired",2],["RedisDriver::extend","Icecave\\Chastity\\Driver\\Redis\\RedisDriver","Icecave\/Chastity\/Driver\/Redis\/RedisDriver.html#method_extend","(string $resource, string $token, integer|<abbr title=\"Icecave\\Chastity\\Driver\\Redis\\float\">float<\/abbr> $ttl)","Extend the TTL of a lock that has already been acquired.",2],["RedisDriver::release","Icecave\\Chastity\\Driver\\Redis\\RedisDriver","Icecave\/Chastity\/Driver\/Redis\/RedisDriver.html#method_release","(string $resource, string $token)","Release a lock.",2],["LockAcquisitionException::__construct","Icecave\\Chastity\\Exception\\LockAcquisitionException","Icecave\/Chastity\/Exception\/LockAcquisitionException.html#method___construct","($resource, <a href=\"http:\/\/php.net\/Exception\"><abbr title=\"Exception\">Exception<\/abbr><\/a> $previous = null)","",2],["LockAlreadyAcquiredException::__construct","Icecave\\Chastity\\Exception\\LockAlreadyAcquiredException","Icecave\/Chastity\/Exception\/LockAlreadyAcquiredException.html#method___construct","($resource, <a href=\"http:\/\/php.net\/Exception\"><abbr title=\"Exception\">Exception<\/abbr><\/a> $previous = null)","",2],["LockNotAcquiredException::__construct","Icecave\\Chastity\\Exception\\LockNotAcquiredException","Icecave\/Chastity\/Exception\/LockNotAcquiredException.html#method___construct","($resource, <a href=\"http:\/\/php.net\/Exception\"><abbr title=\"Exception\">Exception<\/abbr><\/a> $previous = null)","",2],["Lock::__construct","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method___construct","(<a href=\"Icecave\/Chastity\/Driver\/BlockingDriverInterface.html\"><abbr title=\"Icecave\\Chastity\\Driver\\BlockingDriverInterface\">BlockingDriverInterface<\/abbr><\/a> $driver, $resource, $token)","",2],["Lock::__destruct","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method___destruct","()","Release this lock.",2],["Lock::resource","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method_resource","()","Get the resource to which this lock applies.",2],["Lock::isAcquired","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method_isAcquired","()","Check if this lock has been acquired.",2],["Lock::acquire","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method_acquire","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire this lock and throw an exception",2],["Lock::tryAcquire","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method_tryAcquire","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire this lock.",2],["Lock::extend","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method_extend","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl)","Extend the TTL of this lock.",2],["Lock::release","Icecave\\Chastity\\Lock","Icecave\/Chastity\/Lock.html#method_release","()","Release this lock.",2],["LockFactory::__construct","Icecave\\Chastity\\LockFactory","Icecave\/Chastity\/LockFactory.html#method___construct","(<a href=\"Icecave\/Chastity\/Driver\/DriverInterface.html\"><abbr title=\"Icecave\\Chastity\\Driver\\DriverInterface\">DriverInterface<\/abbr><\/a> $driver, <abbr title=\"Icecave\\Druid\\UuidGeneratorInterface\">UuidGeneratorInterface<\/abbr> $uuidGenerator = null)","",2],["LockFactory::defaultTtl","Icecave\\Chastity\\LockFactory","Icecave\/Chastity\/LockFactory.html#method_defaultTtl","()","Get the default TTL to use when acquiring locks.",2],["LockFactory::setDefaultTtl","Icecave\\Chastity\\LockFactory","Icecave\/Chastity\/LockFactory.html#method_setDefaultTtl","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl)","Set the default TTL to use when acquiring locks.",2],["LockFactory::create","Icecave\\Chastity\\LockFactory","Icecave\/Chastity\/LockFactory.html#method_create","(string $resource)","Create a lock object for the given resource.",2],["LockFactory::acquire","Icecave\\Chastity\\LockFactory","Icecave\/Chastity\/LockFactory.html#method_acquire","(string $resource, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr>|null $ttl = null, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire a lock and throw an exception if",2],["LockFactory::tryAcquire","Icecave\\Chastity\\LockFactory","Icecave\/Chastity\/LockFactory.html#method_tryAcquire","(string $resource, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr>|null $ttl = null, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire a lock.",2],["LockFactoryInterface::create","Icecave\\Chastity\\LockFactoryInterface","Icecave\/Chastity\/LockFactoryInterface.html#method_create","(string $resource)","Create a lock object for the given resource.",2],["LockFactoryInterface::defaultTtl","Icecave\\Chastity\\LockFactoryInterface","Icecave\/Chastity\/LockFactoryInterface.html#method_defaultTtl","()","Get the default TTL to use when acquiring locks.",2],["LockFactoryInterface::setDefaultTtl","Icecave\\Chastity\\LockFactoryInterface","Icecave\/Chastity\/LockFactoryInterface.html#method_setDefaultTtl","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl)","Set the default TTL to use when acquiring locks.",2],["LockFactoryInterface::acquire","Icecave\\Chastity\\LockFactoryInterface","Icecave\/Chastity\/LockFactoryInterface.html#method_acquire","(string $resource, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr>|null $ttl = null, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire a lock and throw an exception if",2],["LockFactoryInterface::tryAcquire","Icecave\\Chastity\\LockFactoryInterface","Icecave\/Chastity\/LockFactoryInterface.html#method_tryAcquire","(string $resource, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr>|null $ttl = null, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire a lock.",2],["LockInterface::__destruct","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method___destruct","()","Release this lock.",2],["LockInterface::resource","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method_resource","()","Get the resource to which this lock applies.",2],["LockInterface::isAcquired","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method_isAcquired","()","Check if this lock has been acquired.",2],["LockInterface::acquire","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method_acquire","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire this lock and throw an exception",2],["LockInterface::tryAcquire","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method_tryAcquire","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire this lock.",2],["LockInterface::extend","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method_extend","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl)","Extend the TTL of this lock.",2],["LockInterface::release","Icecave\\Chastity\\LockInterface","Icecave\/Chastity\/LockInterface.html#method_release","()","Release this lock.",2],["ReentrantLock::__construct","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method___construct","(<a href=\"Icecave\/Chastity\/LockInterface.html\"><abbr title=\"Icecave\\Chastity\\LockInterface\">LockInterface<\/abbr><\/a> $lock)","",2],["ReentrantLock::__destruct","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method___destruct","()","Release this lock.",2],["ReentrantLock::resource","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method_resource","()","Get the resource to which this lock applies.",2],["ReentrantLock::isAcquired","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method_isAcquired","()","Check if this lock has been acquired.",2],["ReentrantLock::acquire","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method_acquire","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire this lock and throw an exception",2],["ReentrantLock::tryAcquire","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method_tryAcquire","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl, integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $timeout = INF)","Attempt to acquire this lock.",2],["ReentrantLock::extend","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method_extend","(integer|<abbr title=\"Icecave\\Chastity\\float\">float<\/abbr> $ttl)","Extend the TTL of this lock.",2],["ReentrantLock::release","Icecave\\Chastity\\ReentrantLock","Icecave\/Chastity\/ReentrantLock.html#method_release","()","Release this lock.",2]]
    }
}
search_data['index']['longSearchIndex'] = search_data['index']['searchIndex']