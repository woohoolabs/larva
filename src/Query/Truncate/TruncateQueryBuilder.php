<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Truncate;

use WoohooLabs\Larva\Connection\ConnectionInterface;

class TruncateQueryBuilder implements TruncateQueryBuilderInterface, TruncateQueryInterface
{
    /**
     * @var string
     */
    private $table = "";

    public static function create(): TruncateQueryBuilderInterface
    {
        return new TruncateQueryBuilder();
    }

    public function table(string $table): TruncateQueryBuilderInterface
    {
        $this->table = $table;

        return $this;
    }

    public function execute(ConnectionInterface $connection): bool
    {
        $sql = $connection->getDriver()->translateTruncateQuery($this);

        return $connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(ConnectionInterface $connection): string
    {
        return $connection->getDriver()->translateTruncateQuery($this)->getSql();
    }

    public function getParams(ConnectionInterface $connection): array
    {
        return $connection->getDriver()->translateTruncateQuery($this)->getParams();
    }

    public function toQuery(): TruncateQueryInterface
    {
        return $this;
    }

    public function getTable(): string
    {
        return $this->table;
    }
}
