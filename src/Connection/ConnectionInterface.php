<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

use Traversable;
use WoohooLabs\Larva\Driver\DriverInterface;

interface ConnectionInterface
{
    public function fetchAll(string $sql, array $params = []): array;

    public function fetch(string $sql, array $params = []): Traversable;

    /**
     * @return mixed
     */
    public function fetchColumn(string $sql, array $params = []);

    public function execute(string $sql, array $params = []): bool;

    public function beginTransaction(): bool;

    public function commit(): bool;

    public function rollback(): bool;

    public function getLastInsertedId(?string $name = null): string;

    public function getDriver(): DriverInterface;

    public function getLog(): array;
}
