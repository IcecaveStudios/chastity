<?php
namespace Icecave\Chastity\Driver\Redis;

use Icecave\Chastity\Driver\AbstractIntegrationTest;
use Predis\Client;

/**
 * @group integration
 */
class RedisDriverIntegrationTest extends AbstractIntegrationTest
{
    /**
     * @return DriverInterface
     */
    public function createDriver()
    {
        $client = new Client;
        $client->connect();
        $client->flushAll();

        return new RedisDriver($client);
    }
}
