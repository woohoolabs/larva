<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class DeleteQueryBuilder implements DeleteQueryBuilderInterface, DeleteQueryInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $from;

    /**
     * @var ConditionBuilder
     */
    private $where;

    public static function create(ConnectionInterface $connection): DeleteQueryBuilderInterface
    {
        return new DeleteQueryBuilder($connection);
    }

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->from = "";
        $this->where = new ConditionBuilder($connection);
    }

    public function from(string $table): DeleteQueryBuilderInterface
    {
        $this->from = $table;

        return $this;
    }

    public function where(Closure $where): DeleteQueryBuilderInterface
    {
        $this->where = new ConditionBuilder($this->connection);

        $where($this->where);

        return $this;
    }

    public function execute(): bool
    {
        $sql = $this->connection->getDriver()->translateDeleteQuery($this);

        return $this->connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(): string
    {
        return $this->connection->getDriver()->translateDeleteQuery($this)->getSql();
    }

    public function getParams(): array
    {
        return $this->connection->getDriver()->translateDeleteQuery($this)->getParams();
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getWhere(): ConditionsInterface
    {
        return $this->where;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
