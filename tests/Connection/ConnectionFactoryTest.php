<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Tests\Connection;

use DomainException;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Larva\Connection\ConnectionFactory;
use WoohooLabs\Larva\Connection\MasterSlaveConnection;
use WoohooLabs\Larva\Connection\MySqlPdoConnection;

class ConnectionFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function createConnectionWhenEmpty(): void
    {
        $factory = new ConnectionFactory([]);

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenEmptyDriver(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenNonexistentDriver(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "non-existent-driver",
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithMissingMaster(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $connection = $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithMissingSlaves(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "master1",
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithEmptySlaves(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "master1",
                    "slaves" => [],
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithSameMasterName(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "connection",
                    "slaves" => [
                        "slave1",
                    ],
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithSameSlaveName(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "master1",
                    "slaves" => [
                        "connection",
                    ],
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithMissingMasterDefinition(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "master1",
                    "slaves" => [
                        "slave1",
                    ],
                ],
                "slave1" => [
                    "driver" => "mysql",
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlaveWithMissingSlaveDefinition(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "master1",
                    "slaves" => [
                        "slave1",
                    ],
                ],
                "master1" => [
                    "driver" => "mysql",
                ],
            ]
        );

        $this->expectException(DomainException::class);

        $factory->createConnection("connection");
    }

    /**
     * @test
     */
    public function createConnectionWhenMasterSlave(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "master-slave",
                    "master" => "master1",
                    "slaves" => [
                        "slave1",
                    ],
                ],
                "master1" => [
                    "driver" => "mysql",
                ],
                "slave1" => [
                    "driver" => "mysql",
                ],
            ]
        );

        $connection = $factory->createConnection("connection");

        $this->assertInstanceOf(MasterSlaveConnection::class, $connection);
    }

    /**
     * @test
     */
    public function createConnectionWhenMySql(): void
    {
        $factory = new ConnectionFactory(
            [
                "connection" => [
                    "driver" => "mysql",
                ],
            ]
        );

        $connection = $factory->createConnection("connection");

        $this->assertInstanceOf(MySqlPdoConnection::class, $connection);
    }
}
