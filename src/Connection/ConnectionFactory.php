<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use DomainException;
use function count;
use function in_array;
use function random_int;

class ConnectionFactory
{
    private array $config;

    public static function createFromFile(string $path): ConnectionFactory
    {
        $config = require $path;

        return new ConnectionFactory($config);
    }

    public static function createFromArray(array $config): ConnectionFactory
    {
        return new ConnectionFactory($config);
    }

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createConnection(string $connectionName): ConnectionInterface
    {
        if (isset($this->config[$connectionName]) === false) {
            throw new DomainException("Connection \"$connectionName\" can not be found!");
        }

        $connection = $this->config[$connectionName];

        if (isset($connection["driver"]) === false) {
            throw new DomainException("Driver for the \"$connectionName\" connection must be supplied!");
        }

        switch ($connection["driver"]) {
            case "master-slave":
                return $this->createMasterSlaveConnectionFromArray($connectionName, $connection);
            case "mysql":
                return $this->createMySqlConnectionFromArray($connection);
            default:
                throw new DomainException("Driver \"{$this->config["driver"]}\" does not exist!");
        }
    }

    private function createMasterSlaveConnectionFromArray(string $name, array $connection): MasterSlaveConnection
    {
        if (isset($connection["master"]) === false) {
            throw new DomainException("Master connection name isn't supplied!");
        }

        if (empty($connection["slaves"])) {
            throw new DomainException("Slave connection names aren't supplied!");
        }

        if ($connection["master"] === $name || in_array($name, $connection["slaves"], true)) {
            throw new DomainException("Master and slave connections can't be the same as the parent connection!");
        }

        $slaveNumber = random_int(0, count($connection["slaves"]) - 1);

        return MasterSlaveConnection::create(
            $this->createConnection($connection["master"]),
            $this->createConnection($connection["slaves"][$slaveNumber])
        );
    }

    /**
     * @param array<string, mixed> $connection
     */
    private function createMySqlConnectionFromArray(array $connection): MySqlPdoConnection
    {
        return new MySqlPdoConnection(
            $connection["host"] ?? "",
            $connection["port"] ?? 3306,
            $connection["database"] ?? "",
            $connection["user"] ?? "",
            $connection["password"] ?? "",
            $connection["charset"] ?? "utf8mb4",
            $connection["collation"] ?? "utf8mb4_unicode_ci",
            $connection["modes"] ?? [],
            $connection["options"] ?? [],
            $connection["log"] ?? false
        );
    }
}
