<?php
namespace Icecave\Chastity\Driver;

use Exception;
use PHPUnit_Framework_TestCase;

/**
 * @group integration
 */
abstract class AbstractIntegrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return DriverInterface
     */
    abstract public function createDriver();

    public function setUp()
    {
        try {
            $this->driver = $this->createDriver();
        } catch (Exception $e) {
            $this->markTestSkipped(
                'Driver could not be created: ' . $e->getMessage()
            );
        }

        $this->assertInstanceOf(
            DriverInterface::class,
            $this->driver
        );

        // Use of this variableindicates that the TTL is not relevant to the test.
        $this->irrelevantTtl = 600;
    }

    public function testAcquire()
    {
        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                $this->irrelevantTtl,
                0
            )
        );

        $this->assertSame(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );
    }

    public function testAcquireTtl()
    {
        $requestedTtl = 0.1;

        $actualTtl = $this->driver->acquire(
            '<resource>',
            '<token-1>',
            0.1,
            0
        );

        // Wait out the TTL.
        usleep($actualTtl * 1000000);

        // And a bit more to account for clock inaccuracies.
        usleep(0.003 * 1000000);

        $this->assertGreaterThan(
            0,
            $actualTtl
        );

        $this->assertLessThanOrEqual(
            $requestedTtl,
            $actualTtl
        );

        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );
    }

    public function testAcquireTimeout()
    {
        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                0.1,
                0
            )
        );

        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0.15 // A little longer than the TTL of the first acquire.
            )
        );
    }

    public function testExtend()
    {
        $requestedAcquireTtl = 0.1;
        $requestedExtendTtl  = 0.2;

        $actualAcquireTtl = $this->driver->acquire(
            '<resource>',
            '<token-1>',
            $requestedAcquireTtl,
            0
        );

        $this->assertGreaterThan(
            0,
            $actualAcquireTtl
        );

        $actualExtendTtl = $this->driver->extend(
            '<resource>',
            '<token-1>',
            $requestedExtendTtl
        );

        $this->assertGreaterThan(
            $actualAcquireTtl,
            $actualExtendTtl
        );

        $this->assertLessThanOrEqual(
            $actualAcquireTtl + $requestedExtendTtl,
            $actualExtendTtl
        );

        // Wait out the original TTL.
        usleep($actualAcquireTtl * 1000000);

        $this->assertSame(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );

        // Wait out the rest of the TTL.
        usleep(($actualExtendTtl - $actualAcquireTtl) * 1000000);

        // And a bit more to account for clock inaccuracies.
        usleep(0.003 * 1000000);

        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );
    }

    public function testExtendFailure()
    {
        $ttl = $this->driver->acquire(
            '<resource>',
            '<token-1>',
            0.1,
            0
        );

        $this->assertGreaterThan(
            0,
            $ttl
        );

        $this->assertSame(
            0,
            $this->driver->extend(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl
            )
        );

        // Wait out the TTL.
        usleep($ttl * 1000000);

        // And a bit more to account for clock inaccuracies.
        usleep(0.003 * 1000000);

        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );
    }

    public function testRelease()
    {
        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                10,
                0
            )
        );

        $this->assertTrue(
            $this->driver->release(
                '<resource>',
                '<token-1>'
            )
        );

        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                10,
                0
            )
        );
    }

    public function testReleaseFailure()
    {
        $this->assertGreaterThan(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                10,
                0
            )
        );

        $this->assertFalse(
            $this->driver->release(
                '<resource>',
                '<token-2>'
            )
        );

        $this->assertSame(
            0,
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );
    }
}
