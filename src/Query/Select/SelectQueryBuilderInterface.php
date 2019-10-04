<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\QueryBuilderInterface;

interface SelectQueryBuilderInterface extends QueryBuilderInterface
{
    public function selectColumns(array $columns, string $prefix = ""): SelectQueryBuilderInterface;

    public function selectColumn(string $column, string $prefix = "", string $alias = ""): SelectQueryBuilderInterface;

    public function selectExpressions(array $expressions): SelectQueryBuilderInterface;

    public function selectExpression(string $expression, string $alias = ""): SelectQueryBuilderInterface;

    public function selectCount(
        string $column = "*",
        string $prefix = "",
        string $alias = "",
        bool $isDistinct = false
    ): SelectQueryBuilderInterface;

    public function distinct(bool $isDistinct = true): SelectQueryBuilderInterface;

    public function from(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function fromSubquery(SelectQueryBuilderInterface $subquery, string $alias): SelectQueryBuilderInterface;

    public function leftJoin(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function rightJoin(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function join(string $table, string $alias = "", string $type = ""): SelectQueryBuilderInterface;

    public function on(ConditionBuilderInterface $on): SelectQueryBuilderInterface;

    public function where(ConditionBuilderInterface $where): SelectQueryBuilderInterface;

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): SelectQueryBuilderInterface;

    public function groupBy(string $attribute): SelectQueryBuilderInterface;

    public function groupByAttributes(array $attributes): SelectQueryBuilderInterface;

    public function having(ConditionBuilderInterface $having): SelectQueryBuilderInterface;

    public function addHavingGroup(ConditionBuilderInterface $having, string $operator = "AND"): SelectQueryBuilderInterface;

    public function orderByAttribute(string $attribute, string $direction = "ASC"): SelectQueryBuilderInterface;

    public function orderByExpression(string $expression, string $direction = "ASC"): SelectQueryBuilderInterface;

    public function lockForShare(): SelectQueryBuilderInterface;

    public function lockForUpdate(): SelectQueryBuilderInterface;

    public function lock(string $mode): SelectQueryBuilderInterface;

    public function union(SelectQueryBuilderInterface $query, bool $distinct = true): SelectQueryBuilderInterface;

    public function limit(?int $limit): SelectQueryBuilderInterface;

    public function offset(?int $offset): SelectQueryBuilderInterface;

    public function fetchAll(ConnectionInterface $connection): array;

    public function fetch(ConnectionInterface $connection): iterable;

    /**
     * @return mixed
     */
    public function fetchColumn(ConnectionInterface $connection);

    public function toQuery(): SelectQueryInterface;
}
