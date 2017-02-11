<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use Closure;
use PDO;
use PDOStatement;
use Traversable;
use WoohooLabs\Larva\Driver\DriverInterface;

abstract class AbstractPdoConnection implements ConnectionInterface
{
    /**
     * @var array
     */
    protected $settings;

    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var Logger
     */
    private $logger;

    abstract protected function createDriver(): DriverInterface;

    protected function __construct(
        string $dsn,
        string $username,
        string $password,
        array $options,
        bool $isLogging
    ) {
        $this->settings = [
            "dsn" => $dsn,
            "username" => $username,
            "password" => $password,
            "options" => $options,
        ];
        $this->driver = $this->createDriver();
        $this->logger = new Logger($isLogging);
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->getPdo()->prepare($sql);
        $this->executePreparedStatement($statement, $sql, $params);

        return $statement->fetchAll();
    }

    public function fetch(string $sql, array $params = []): Traversable
    {
        $statement = $this->getPdo()->prepare($sql);
        $this->executePreparedStatement($statement, $sql, $params);

        while ($statement->nextRowset()) {
            yield $statement->fetch();
        }
    }

    public function fetchColumn(string $sql, array $params = []): string
    {
        $statement = $this->getPdo()->prepare($sql);
        $this->executePreparedStatement($statement, $sql, $params);

        return $statement->fetchColumn();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $statement = $this->getPdo()->prepare($sql);

        return $this->executePreparedStatement($statement, $sql, $params);
    }

    public function beginTransaction(): bool
    {
        return $this->executeSql(
            "BEGIN",
            [],
            function () {
                return $this->getPdo()->beginTransaction();
            }
        );
    }

    public function commit(): bool
    {
        return $this->executeSql(
            "COMMIT",
            [],
            function () {
                return $this->getPdo()->beginTransaction();
            }
        );
    }

    public function rollback(): bool
    {
        return $this->executeSql(
            "ROLLBACK",
            [],
            function () {
                return $this->getPdo()->beginTransaction();
            }
        );
    }

    public function getLastInsertedId(string $name = null): string
    {
        return $this->getPdo()->lastInsertId($name);
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function getLog(): array
    {
        return $this->logger->getLog();
    }

    public function getPdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO(
                $this->settings["dsn"],
                $this->settings["username"],
                $this->settings["password"],
                $this->settings["options"] + $this->getDefaultAttributes()
            );
        }

        return $this->pdo;
    }

    private function getDefaultAttributes(): array
    {
        return [
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];
    }

    private function executePreparedStatement(PDOStatement $statement, string $sql, array $params): bool
    {
        foreach ($params as $key => $value) {
            if ($value === null) {
                $bindType = PDO::PARAM_NULL;
            } elseif (is_int($value) || is_float($value)) {
                $bindType = PDO::PARAM_INT;
            } else {
                $bindType = PDO::PARAM_STR;
            }

            $bindKey = is_string($key) ? $key : $key + 1;

            $statement->bindValue($bindKey, $value, $bindType);
        }

        return $this->executeSql(
            $sql,
            $params,
            function () use ($statement) {
                return $statement->execute();
            }
        );
    }

    private function executeSql(string $sql, array $params, Closure $query): bool
    {
        $t1 = $this->logger->getTime();

        $result = $query();

        $t2 = $this->logger->getTime();

        $this->logger->log($sql, $result, $params, $t1, $t2);

        return $result;
    }
}
