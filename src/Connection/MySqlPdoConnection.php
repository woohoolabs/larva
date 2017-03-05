<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use PDO;
use WoohooLabs\Larva\Driver\DriverInterface;
use WoohooLabs\Larva\Driver\MySql\MySqlDriver;

class MySqlPdoConnection extends AbstractPdoConnection
{
    public function __construct(
        string $host,
        int $port,
        string $database,
        string $username,
        string $password,
        string $charset = "utf8mb4",
        string $collation = "utf8mb4_unicode_ci",
        array $modes = [],
        array $options = [],
        bool $isLogging = false
    ) {
        $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
        $options[PDO::MYSQL_ATTR_INIT_COMMAND] = self::getInitCommand($charset, $collation, $modes);

        parent::__construct($dsn, $username, $password, $options, $isLogging);
    }

    protected function createDriver(): DriverInterface
    {
        return new MySqlDriver();
    }

    private static function getInitCommand(string $charset, string $collation, array $modes): string
    {
        $defaultModes = [
            "ONLY_FULL_GROUP_BY",
            "STRICT_TRANS_TABLES",
            "NO_ZERO_IN_DATE",
            "NO_ZERO_DATE",
            "ERROR_FOR_DIVISION_BY_ZERO",
            "NO_AUTO_CREATE_USER",
            "NO_ENGINE_SUBSTITUTION",
        ];
        $modesString = implode(",", $modes + $defaultModes);

        return "SET SESSION SQL_MODE='$modesString', NAMES '$charset' COLLATE '$collation';";
    }
}
