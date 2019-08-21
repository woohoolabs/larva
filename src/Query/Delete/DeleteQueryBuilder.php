<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class DeleteQueryBuilder implements DeleteQueryBuilderInterface, DeleteQueryInterface
{
    private string $from = "";

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
        $this->where = $where->toConditions();

        return $this;
    }

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): DeleteQueryBuilderInterface
    {
        if ($this->where === null) {
            $this->where = new ConditionBuilder();
        }

        $this->where->addConditionGroup($where, $operator);

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

    public function toQuery(): DeleteQueryInterface
    {
        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getWhere(): ?ConditionsInterface
    {
        return $this->where;
    }
}
