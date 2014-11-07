<?php
namespace Icecave\Chastity\Redis;

use Icecave\Chastity\LockInterface;
use Icecave\Chastity\LockTrait;

class RedisLock implements LockInterface
{
    use LockTrait;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get the name of this lock.
     *
     * @return string The name of this lock.
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Acquire this lock.
     *
     * @param integer|float|null $timeout
     *
     * @return boolean
     */
    protected function acquireLogic($timeout)
    {
        throw new \Exception('Not implemented.');
    }

    /**
     * Release this lock.
     */
    protected function releaseLogic()
    {
        throw new \Exception('Not implemented.');
    }

    private $name;
}
