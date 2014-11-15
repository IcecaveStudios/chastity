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
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                $this->irrelevantTtl,
                0
            )
        );

        $this->assertFalse(
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
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                1,
                0
            )
        );

        usleep(1.1 * 1000000);

        $this->assertTrue(
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
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                1,
                0
            )
        );

        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                2
            )
        );
    }

    /**
     * @large
     */
    public function testExtend()
    {
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                1,
                0
            )
        );

        $this->assertTrue(
            $this->driver->extend(
                '<resource>',
                '<token-1>',
                1
            )
        );

        usleep(1.1 * 1000000);

        $this->assertFalse(
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );

        usleep(1 * 1000000);

        $this->assertTrue(
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
        $this->assertTrue(
            $this->driver->acquire(
                '<resource>',
                '<token-1>',
                1,
                0
            )
        );

        $this->assertFalse(
            $this->driver->extend(
                '<resource>',
                '<token-2>',
                10
            )
        );

        usleep(1.1 * 1000000);

        $this->assertTrue(
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
        $this->assertTrue(
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

        $this->assertTrue(
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
        $this->assertTrue(
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

        $this->assertFalse(
            $this->driver->acquire(
                '<resource>',
                '<token-2>',
                $this->irrelevantTtl,
                0
            )
        );
    }
}
