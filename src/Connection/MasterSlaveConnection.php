<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use Traversable;
use WoohooLabs\Larva\Driver\DriverInterface;
use WoohooLabs\Larva\Driver\MasterSlaveDriver;

class MasterSlaveConnection implements ConnectionInterface
{
    /**
     * @var ConnectionInterface
     */
    private $masterConnection;

    /**
     * @var ConnectionInterface
     */
    private $slaveConnection;

    public static function create(
        ConnectionInterface $masterConnection,
        ConnectionInterface $slaveConnection
    ): MasterSlaveConnection {
        return new MasterSlaveConnection($masterConnection, $slaveConnection);
    }

    public function __construct(ConnectionInterface $masterConnection, ConnectionInterface $slaveConnection)
    {
        $this->masterConnection = $masterConnection;
        $this->slaveConnection = $slaveConnection;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->slaveConnection->fetchAll($sql, $params);
    }

    public function fetch(string $sql, array $params = []): Traversable
    {
        return $this->slaveConnection->fetch($sql, $params);
    }

    public function fetchColumn(string $sql, array $params = []): string
    {
        return $this->slaveConnection->fetchColumn($sql, $params);
    }

    public function execute(string $sql, array $params = []): bool
    {
        return $this->masterConnection->execute($sql, $params);
    }

    public function beginTransaction(): bool
    {
        return $this->masterConnection->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->masterConnection->commit();
    }

    public function rollback(): bool
    {
        return $this->masterConnection->rollback();
    }

    public function getLastInsertedId(string $name = null): string
    {
        return $this->masterConnection->getLastInsertedId();
    }

    public function getDriver(): DriverInterface
    {
        return new MasterSlaveDriver($this->masterConnection->getDriver(), $this->slaveConnection->getDriver());
    }

    public function getLog(): array
    {
        $masterConnectionLog = $this->masterConnection->getLog();
        $slaveConnectionLog = $this->masterConnection->getLog();

        return array_merge($masterConnectionLog, $slaveConnectionLog);
    }
}
