<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use DomainException;
use function count;
use function in_array;
use function random_int;

class ConnectionFactory
{
    /**
     * @var array
     */
    private $config;

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

    private function createMasterSlaveConnectionFromArray(string $name, array $array): MasterSlaveConnection
    {
        if (isset($array["master"]) === false) {
            throw new DomainException("Master connection name isn't supplied!");
        }

        if (isset($array["slaves"]) === false) {
            throw new DomainException("Slave connection names aren't supplied!");
        }

        if ($array["master"] === $name || in_array($name, $array["slaves"], true)) {
            throw new DomainException("Master and slave connections can't be the same as the parent connection!");
        }

        $slaveNumber = random_int(0, count($array["slaves"]));

        return MasterSlaveConnection::create(
            $this->createConnection($array["master"]),
            $this->createConnection($array["slaves"][$slaveNumber])
        );
    }

    private function createMySqlConnectionFromArray(array $array): MySqlPdoConnection
    {
        return new MySqlPdoConnection(
            $array["host"] ?? "",
            $array["port"] ?? 3306,
            $array["database"] ?? "",
            $array["user"] ?? "",
            $array["password"] ?? "",
            $array["charset"] ?? "utf8mb4",
            $array["collation"] ?? "utf8mb4_unicode_ci",
            $array["modes"] ?? [],
            $array["options"] ?? [],
            $array["log"] ?? false
        );
    }
}
