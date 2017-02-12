<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class DeleteQueryBuilder implements DeleteQueryBuilderInterface, DeleteQueryInterface
{
    /**
     * @var string
     */
    private $from = "";

    /**
     * @var ConditionsInterface|null
     */
    private $where;

    public static function create(): DeleteQueryBuilderInterface
    {
        return new DeleteQueryBuilder();
    }

    public function from(string $table): DeleteQueryBuilderInterface
    {
        $this->from = $table;

        return $this;
    }

    public function where(ConditionBuilderInterface $where): DeleteQueryBuilderInterface
    {
        $this->where = $where->getQueryConditions();

        return $this;
    }

    public function execute(ConnectionInterface $connection): bool
    {
        $sql = $connection->getDriver()->translateDeleteQuery($this);

        return $connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(ConnectionInterface $connection): string
    {
        return $connection->getDriver()->translateDeleteQuery($this)->getSql();
    }

    public function getParams(ConnectionInterface $connection): array
    {
        return $connection->getDriver()->translateDeleteQuery($this)->getParams();
    }

    public function getQuery(): DeleteQueryInterface
    {
        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @return ConditionsInterface|null
     */
    public function getWhere()
    {
        return $this->where;
    }
}
