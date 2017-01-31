<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use Closure;
use Traversable;
use WoohooLabs\Larva\Connection\ConnectionInterface;

interface SelectQueryBuilderInterface
{
    public function select(array $expressions): SelectQueryBuilderInterface;

    public function selectExpression(string $expression, string $alias = ""): SelectQueryBuilderInterface;

    public function selectColumn(string $column, string $prefix = "", string $alias = ""): SelectQueryBuilderInterface;

    public function distinct(bool $isDistinct = true): SelectQueryBuilderInterface;

    public function from(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function fromSubquery(Closure $subquery, string $alias): SelectQueryBuilderInterface;

    public function leftJoin(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function rightJoin(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function join(string $table, string $alias = "", string $type = ""): SelectQueryBuilderInterface;

    public function on(Closure $on): SelectQueryBuilderInterface;

    public function where(Closure $where): SelectQueryBuilderInterface;

    public function groupBy(string $attribute): SelectQueryBuilderInterface;

    public function groupByAttributes(array $attributes): SelectQueryBuilderInterface;

    public function having(Closure $having): SelectQueryBuilderInterface;

    public function orderBy(string $attribute, string $direction = "ASC"): SelectQueryBuilderInterface;

    public function lock(string $mode): SelectQueryBuilderInterface;

    /**
     * @param int|null $limit
     */
    public function limit($limit): SelectQueryBuilderInterface;

    /**
     * @param int|null $offset
     */
    public function offset($offset): SelectQueryBuilderInterface;

    public function fetchAll(): array;

    public function fetch(): Traversable;

    public function fetchColumn(): string;

    public function getSql(): string;

    public function getParams(): array;

    public function getConnection(): ConnectionInterface;
}
