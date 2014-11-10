<?php
namespace Icecave\Chastity\Driver\Redis;

use Exception;
use PHPUnit_Framework_TestCase;
use Predis\Client;

/**
 * @group integration
 */
class RedisDriverIntegrationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        try {
            $this->redisClient = new Client;
            $this->redisClient->connect();
        } catch (Exception $e) {
            $this->markTestSkipped('Redis server is unavailable.');
        }

        $this->redisClient->flushall();

        $this->driver = new RedisDriver($this->redisClient);
    }

    public function testAcquire()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token>',
                10
            )
        );
    }

    public function testAcquireFailure()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token>',
                10
            )
        );

        $this->assertFalse(
            $this->driver->acquire(
                '<resource>',
                '<different-token>',
                10
            )
        );
    }

    /**
     * @large
     */
    public function testAcquireTtl()
    {
        $this->driver->acquire(
            '<resource>',
            '<token>',
            0.1
        );

        usleep(0.2 * 1000000);

        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<token>')
        );
    }

    public function testIsAcquired()
    {
        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<token>')
        );

        $this->driver->acquire(
            '<resource>',
            '<token>',
            10
        );

        $this->assertTrue(
            $this->driver->isAcquired('<resource>', '<token>')
        );

        $this->driver->release(
            '<resource>',
            '<token>'
        );

        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<token>')
        );
    }

    /**
     * @group large
     */
    public function testExtend()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token>',
                0.1
            )
        );

        $this->assertTrue(
            $this->driver->extend('<resource>', '<token>', 0.2)
        );

        usleep(0.1 * 1000000);

        $this->assertTrue(
            $this->driver->isAcquired('<resource>', '<token>')
        );

        usleep(0.3 * 1000000);

        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<token>')
        );
    }

    /**
     * @large
     */
    public function testExtendFailure()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token>',
                0.1
            )
        );

        $this->assertFalse(
            $this->driver->extend(
                '<resource>',
                '<different-token>',
                10
            )
        );

        usleep(0.2 * 1000000);

        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<token>')
        );
    }

    public function testRelease()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token>',
                10
            )
        );

        $this->assertTrue(
            $this->driver->release(
                '<resource>',
                '<token>'
            )
        );

        $this->assertFalse(
            $this->driver->isAcquired('<resource>', '<token>')
        );
    }

    public function testReleaseFailure()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token>',
                10
            )
        );

        $this->assertFalse(
            $this->driver->release(
                '<resource>',
                '<different-token>'
            )
        );

        $this->assertTrue(
            $this->driver->isAcquired('<resource>', '<token>')
        );
    }
}
