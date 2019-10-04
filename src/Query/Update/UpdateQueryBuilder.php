<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class UpdateQueryBuilder implements UpdateQueryBuilderInterface, UpdateQueryInterface
{
    private array $table = [];
    private array $values = [];
    private ?ConditionsInterface $where;

    public static function create(): UpdateQueryBuilderInterface
    {
        return new UpdateQueryBuilder();
    }

    public function __construct()
    {
        $this->where = new ConditionBuilder();
    }

    public function table(string $table, string $alias = ""): UpdateQueryBuilderInterface
    {
        $this->table = [
            "table" => $table,
            "alias" => $alias,
        ];

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setValue(string $column, $value): UpdateQueryBuilderInterface
    {
        $this->values[] = [
            "type" => "value",
            "column" => $column,
            "value" => $value,
        ];

        return $this;
    }

    public function setValues(array $values): UpdateQueryBuilderInterface
    {
        foreach ($values as $column => $value) {
            $this->setValue($column, $value);
        }

        return $this;
    }

    public function setRawValue(string $column, string $value, array $params = []): UpdateQueryBuilderInterface
    {
        $this->values[] = [
            "type" => "raw",
            "column" => $column,
            "value" => $value,
            "params" => $params,
        ];

        return $this;
    }

    public function setRawValues(array $values, array $params = []): UpdateQueryBuilderInterface
    {
        foreach ($values as $column => $value) {
            $this->setRawValue($column, $value);
        }

        return $this;
    }

    public function where(ConditionBuilderInterface $where): UpdateQueryBuilderInterface
    {
        $this->where = $where->toConditions();

        return $this;
    }

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): UpdateQueryBuilderInterface
    {
        if ($this->where === null) {
            $this->where = new ConditionBuilder();
        }

        $this->where->addConditionGroup($where, $operator);

        return $this;
    }

    public function execute(ConnectionInterface $connection): bool
    {
        $sql = $connection->getDriver()->translateUpdateQuery($this);

        return $connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(ConnectionInterface $connection): string
    {
        return $connection->getDriver()->translateUpdateQuery($this)->getSql();
    }

    public function getParams(ConnectionInterface $connection): array
    {
        return $connection->getDriver()->translateUpdateQuery($this)->getParams();
    }

    public function toQuery(): UpdateQueryInterface
    {
        return $this;
    }

    public function getTable(): array
    {
        return $this->table;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function getWhere(): ?ConditionsInterface
    {
        return $this->where;
    }
}
