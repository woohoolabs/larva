<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use Closure;
use Traversable;

interface SelectQueryBuilderInterface
{
    public function fields(array $fields): SelectQueryBuilderInterface;

    public function distinct(bool $isDistinct = true): SelectQueryBuilderInterface;

    public function from(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function fromSubquery(Closure $subquery, string $alias): SelectQueryBuilderInterface;

    public function where(Closure $condition): SelectQueryBuilderInterface;

    public function leftJoin(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function rightJoin(string $table, string $alias = ""): SelectQueryBuilderInterface;

    public function join(string $table, string $alias = "", string $type = ""): SelectQueryBuilderInterface;

    public function on(Closure $condition): SelectQueryBuilderInterface;

    public function having(Closure $condition): SelectQueryBuilderInterface;

    public function groupBy(string $attribute): SelectQueryBuilderInterface;

    public function groupByAttributes(array $attributes): SelectQueryBuilderInterface;

    public function orderBy(string $attribute, string $direction = "ASC"): SelectQueryBuilderInterface;

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
}
