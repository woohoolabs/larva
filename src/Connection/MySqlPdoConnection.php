<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use PDO;
use WoohooLabs\Larva\Driver\Driver;
use WoohooLabs\Larva\Driver\DriverInterface;
use WoohooLabs\Larva\Driver\Mysql\MySqlConditionsTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlDeleteTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlInsertTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlSelectTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlUpdateTranslator;

class MySqlPdoConnection extends AbstractPdoConnection
{
    public static function create(
        string $driver,
        string $host,
        int $port,
        string $database,
        string $username,
        string $password,
        string $charset = "utf8mb4",
        string $collation = "utf8mb4_unicode_ci",
        array $modes = [],
        array $options = [],
        bool $isLogging
    ): ConnectionInterface {
        $dsn = "$driver:host=$host;dbname=$database;port=$port;charset=$charset";
        $options[PDO::MYSQL_ATTR_INIT_COMMAND] = self::getInitCommand($charset, $collation, $modes);

        return new MySqlPdoConnection($dsn, $username, $password, $options, $isLogging);
    }

    protected function createDriver(): DriverInterface
    {
        $conditionsTranslator = new MySqlConditionsTranslator();
        $selectTranslator = new MySqlSelectTranslator($conditionsTranslator);
        $insertTranslator = new MySqlInsertTranslator($selectTranslator);
        $updateTranslator = new MySqlUpdateTranslator($conditionsTranslator);
        $deleteTranslator = new MySqlDeleteTranslator($conditionsTranslator);

        return new Driver($selectTranslator, $insertTranslator, $updateTranslator, $deleteTranslator);
    }

    private static function getInitCommand(string $charset, string $collation, array $modes)
    {
        // Setup modes
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
