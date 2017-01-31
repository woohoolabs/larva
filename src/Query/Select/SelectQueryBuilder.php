<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use Closure;
use Traversable;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class SelectQueryBuilder implements SelectQueryBuilderInterface, SelectQueryInterface
{
    /**
     * @var \WoohooLabs\Larva\Connection\ConnectionInterface
     */
    private $connection;

    /**
     * @var bool
     */
    private $distinct = false;

    /**
     * @var array
     */
    private $select = [];

    /**
     * @var array
     */
    private $from = [];

    /**
     * @var array
     */
    private $aggregate = [];

    /**
     * @var array
     */
    private $join = [];

    /**
     * @var ConditionBuilder
     */
    private $where;

    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @var ConditionBuilder
     */
    private $having;

    /**
     * @var array
     */
    private $orderBy = [];

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var int|null
     */
    private $offset;

    /**
     * @var array
     */
    private $union = [];

    /**
     * @var string
     */
    private $lock = "";

    /**
     * @var array
     */
    private $params = [];

    public static function create(ConnectionInterface $connection): SelectQueryBuilderInterface
    {
        return new SelectQueryBuilder($connection);
    }

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->where = new ConditionBuilder($this->connection);
        $this->having = new ConditionBuilder($this->connection);
    }

    public function select(array $expressions): SelectQueryBuilderInterface
    {
        foreach ($expressions as $field) {
            $this->selectExpression($field);
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

    public function fromSubquery(Closure $subquery, string $alias): SelectQueryBuilderInterface
    {
        $queryBuilder = new SelectQueryBuilder($this->connection);
        $subquery($queryBuilder);

        $this->from = [
            "type" => "subquery",
            "table" => $queryBuilder,
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

    public function on(Closure $on): SelectQueryBuilderInterface
    {
        $queryBuilder = new ConditionBuilder($this->connection);
        $on($queryBuilder);

        $this->join[] = [
            "type" => "on",
            "on" => $queryBuilder,
        ];

        return $this;
    }

    public function where(Closure $where): SelectQueryBuilderInterface
    {
        $where($this->where);

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

    public function having(Closure $having): SelectQueryBuilderInterface
    {
        $having($this->having);

        return $this;
    }

    public function orderBy(string $attribute, string $direction = "ASC"): SelectQueryBuilderInterface
    {
        $this->orderBy[] = ["attribute" => $attribute, "direction" => $direction];

        return $this;
    }

    public function limit($limit): SelectQueryBuilderInterface
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset): SelectQueryBuilderInterface
    {
        $this->offset = $offset;

        return $this;
    }

    public function lock(string $mode)
    {
        $this->lock = $mode;
    }

    public function fetchAll(): array
    {
        $query = $this->connection->getDriver()->translateSelectQuery($this);

        return $this->connection->fetchAll($query->getSql(), $query->getParams());
    }

    public function fetch(): Traversable
    {
        $query = $this->connection->getDriver()->translateSelectQuery($this);

        return $this->connection->fetch($query->getSql(), $query->getParams());
    }

    public function fetchColumn(): string
    {
        $query = $this->connection->getDriver()->translateSelectQuery($this);

        return $this->connection->fetchColumn($query->getSql(), $query->getParams());
    }

    public function getSql(): string
    {
        return $this->connection->getDriver()->translateSelectQuery($this)->getSql();
    }

    public function getParams(): array
    {
        return $this->connection->getDriver()->translateSelectQuery($this)->getParams();
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

    public function getWhere(): ConditionsInterface
    {
        return $this->where;
    }

    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    public function getHaving(): ConditionsInterface
    {
        return $this->having;
    }

    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    public function getUnion(): array
    {
        return $this->union;
    }

    public function getLock(): string
    {
        return $this->lock;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
