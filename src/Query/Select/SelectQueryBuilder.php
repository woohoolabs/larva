<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class SelectQueryBuilder implements SelectQueryBuilderInterface, SelectQueryInterface
{
    private bool $distinct = false;
    private array $select = [];
    private array $from = [];
    private array $aggregate = [];
    private array $join = [];
    private ?ConditionsInterface $where = null;
    private array $groupBy = [];
    private ?ConditionsInterface $having = null;
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    private array $unions = [];
    private array $lock = [];

    public static function create(): SelectQueryBuilderInterface
    {
        return new SelectQueryBuilder();
    }

    public function selectColumns(array $columns, string $prefix = ""): SelectQueryBuilderInterface
    {
        foreach ($columns as $field) {
            $this->selectColumn($field, $prefix);
        }

        return $this;
    }

    public function selectColumn(string $column, string $prefix = "", string $alias = ""): SelectQueryBuilderInterface
    {
        $this->select[] = [
            "type" => "column",
            "prefix" => $prefix,
            "expression" => $column,
            "alias" => $alias,
        ];

        return $this;
    }

    public function selectExpressions(array $expressions): SelectQueryBuilderInterface
    {
        foreach ($expressions as $expression) {
            $this->selectExpression($expression);
        }

        return $this;
    }

    public function selectExpression(string $expression, string $alias = ""): SelectQueryBuilderInterface
    {
        $this->select[] = [
            "type" => "expression",
            "prefix" => "",
            "expression" => $expression,
            "alias" => $alias,
        ];

        return $this;
    }

    public function selectCount(
        string $column = "*",
        string $prefix = "",
        string $alias = "",
        bool $isDistinct = false
    ): SelectQueryBuilderInterface {
        $this->select[] = [
            "type" => "count",
            "prefix" => $prefix,
            "expression" => $column,
            "distinct" => $isDistinct,
            "alias" => $alias,
        ];

        return $this;
    }

    public function distinct(bool $isDistinct = true): SelectQueryBuilderInterface
    {
        $this->distinct = $isDistinct;

        return $this;
    }

    public function from(string $table, string $alias = ""): SelectQueryBuilderInterface
    {
        $this->from = [
            "type" => "table",
            "table" => $table,
            "alias" => $alias,
        ];

        return $this;
    }

    public function fromSubquery(SelectQueryBuilderInterface $subquery, string $alias): SelectQueryBuilderInterface
    {
        $this->from = [
            "type" => "subquery",
            "table" => $subquery->toQuery(),
            "alias" => $alias,
        ];

        return $this;
    }

    public function leftJoin(string $table, string $alias = ""): SelectQueryBuilderInterface
    {
        return $this->join($table, $alias, "LEFT");
    }

    public function rightJoin(string $table, string $alias = ""): SelectQueryBuilderInterface
    {
        return $this->join($table, $alias, "RIGHT");
    }

    public function join(string $table, string $alias = "", string $type = ""): SelectQueryBuilderInterface
    {
        $this->join[] = [
            "type" => $type,
            "table" => $table,
            "alias" => $alias,
        ];

        return $this;
    }

    public function on(ConditionBuilderInterface $on): SelectQueryBuilderInterface
    {
        $this->join[] = [
            "type" => "on",
            "on" => $on->toConditions(),
        ];

        return $this;
    }

    public function where(ConditionBuilderInterface $where): SelectQueryBuilderInterface
    {
        $this->where = $where->toConditions();

        return $this;
    }

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): SelectQueryBuilderInterface
    {
        if ($this->where === null) {
            $this->where = new ConditionBuilder();
        }

        $this->where->addConditionGroup($where, $operator);

        return $this;
    }

    public function groupBy(string $attribute): SelectQueryBuilderInterface
    {
        $this->groupBy[] = $attribute;

        return $this;
    }

    public function groupByAttributes(array $attributes): SelectQueryBuilderInterface
    {
        foreach ($attributes as $attribute) {
            $this->groupBy($attribute);
        }

        return $this;
    }

    public function having(ConditionBuilderInterface $having): SelectQueryBuilderInterface
    {
        $this->having = $having->toConditions();

        return $this;
    }

    public function addHavingGroup(ConditionBuilderInterface $having, string $operator = "AND"): SelectQueryBuilderInterface
    {
        if ($this->having === null) {
            $this->having = new ConditionBuilder();
        }

        $this->having->addConditionGroup($having, $operator);

        return $this;
    }

    public function orderByAttribute(string $attribute, string $direction = "ASC"): SelectQueryBuilderInterface
    {
        $this->orderBy[] = ["type" => "attribute", "attribute" => $attribute, "direction" => $direction];

        return $this;
    }

    public function orderByExpression(string $expression, string $direction = "ASC"): SelectQueryBuilderInterface
    {
        $this->orderBy[] = ["type" => "expression", "expression" => $expression, "direction" => $direction];

        return $this;
    }

    public function limit(?int $limit): SelectQueryBuilderInterface
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(?int $offset): SelectQueryBuilderInterface
    {
        $this->offset = $offset;

        return $this;
    }

    public function lockForShare(): SelectQueryBuilderInterface
    {
        $this->lock = [
            "type" => "share",
        ];

        return $this;
    }

    public function lockForUpdate(): SelectQueryBuilderInterface
    {
        $this->lock = [
            "type" => "update",
        ];

        return $this;
    }

    public function lock(string $mode): SelectQueryBuilderInterface
    {
        $this->lock = [
            "type" => "custom",
            "mode" => $mode,
        ];

        return $this;
    }

    public function union(SelectQueryBuilderInterface $query, bool $distinct = true): SelectQueryBuilderInterface
    {
        $this->unions[] = [
            "query" => $query->toQuery(),
            "distinct" => $distinct,
        ];

        return $this;
    }

    public function fetchAll(ConnectionInterface $connection): array
    {
        $query = $connection->getDriver()->translateSelectQuery($this);

        return $connection->fetchAll($query->getSql(), $query->getParams());
    }

    public function fetch(ConnectionInterface $connection): iterable
    {
        $query = $connection->getDriver()->translateSelectQuery($this);

        return $connection->fetch($query->getSql(), $query->getParams());
    }

    /**
     * @return mixed
     */
    public function fetchColumn(ConnectionInterface $connection)
    {
        $query = $connection->getDriver()->translateSelectQuery($this);

        return $connection->fetchColumn($query->getSql(), $query->getParams());
    }

    public function getSql(ConnectionInterface $connection): string
    {
        return $connection->getDriver()->translateSelectQuery($this)->getSql();
    }

    public function getParams(ConnectionInterface $connection): array
    {
        return $connection->getDriver()->translateSelectQuery($this)->getParams();
    }

    public function toQuery(): SelectQueryInterface
    {
        return $this;
    }

    public function getSelectExpressions(): array
    {
        return $this->select;
    }

    public function isDistinct(): bool
    {
        return $this->distinct;
    }

    public function getFrom(): array
    {
        return $this->from;
    }

    public function getAggregate(): array
    {
        return $this->aggregate;
    }

    public function getJoins(): array
    {
        return $this->join;
    }

    public function getWhere(): ?ConditionsInterface
    {
        return $this->where;
    }

    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function getHaving(): ?ConditionsInterface
    {
        return $this->having;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getUnions(): array
    {
        return $this->unions;
    }

    public function getLock(): array
    {
        return $this->lock;
    }
}
