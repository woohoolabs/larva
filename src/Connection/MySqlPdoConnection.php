<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use WoohooLabs\Larva\Driver\Driver;
use WoohooLabs\Larva\Driver\DriverInterface;
use WoohooLabs\Larva\Driver\Mysql\MySqlConditionsTranslator;
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

        $self = new MySqlPdoConnection($dsn, $username, $password, $options, $isLogging);
        $self->setCharset($charset, $collation);
        $self->setModes($modes);

        return $self;
    }

    public function getDriver(): DriverInterface
    {
        $conditionsTranslator = new MySqlConditionsTranslator();
        $selectTranslator = new MySqlSelectTranslator($conditionsTranslator);
        $insertTranslator = new MySqlInsertTranslator($selectTranslator);
        $updateTranslator = new MySqlUpdateTranslator($conditionsTranslator);

        return new Driver($selectTranslator, $insertTranslator, $updateTranslator);
    }

    private function setCharset(string $charset, string $collation)
    {
        if (empty($charset)) {
            return;
        }

        $collation = $collation ? $collation : "";
        $this->execute("SET NAMES '$charset' COLLATE '$collation'");
    }

    private function setModes(array $modes)
    {
        if (empty($modes)) {
            $modes = [
                "ONLY_FULL_GROUP_BY",
                "STRICT_TRANS_TABLES",
                "NO_ZERO_IN_DATE",
                "NO_ZERO_DATE",
                "ERROR_FOR_DIVISION_BY_ZERO",
                "NO_AUTO_CREATE_USER",
                "NO_ENGINE_SUBSTITUTION",
            ];
        }

        $modesString = implode(",", $modes);
        $this->execute("SET SESSION SQL_MODE='$modesString'");
    }
}
