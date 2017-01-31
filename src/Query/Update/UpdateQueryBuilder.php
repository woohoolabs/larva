<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class UpdateQueryBuilder implements UpdateQueryBuilderInterface, UpdateQueryInterface
{
    /**
     * @var \WoohooLabs\Larva\Connection\ConnectionInterface
     */
    private $connection;

    /**
     * @var array
     */
    private $table;

    /**
     * @var array
     */
    private $values;

    /**
     * @var ConditionBuilder
     */
    private $where;

    public static function create(ConnectionInterface $connection): UpdateQueryBuilderInterface
    {
        return new UpdateQueryBuilder($connection);
    }

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->table = [];
        $this->values = [];
        $this->where = new ConditionBuilder($connection);
    }

    public function table(string $table, string $alias = ""): UpdateQueryBuilderInterface
    {
        $this->table = [
            "table" => $table,
            "alias" => $alias,
        ];

        return $this;
    }

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

    public function where(Closure $where): UpdateQueryBuilderInterface
    {
        $this->where = new ConditionBuilder($this->connection);

        $where($this->where);

        return $this;
    }

    public function execute(): bool
    {
        $sql = $this->connection->getDriver()->translateUpdateQuery($this);

        return $this->connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(): string
    {
        return $this->connection->getDriver()->translateUpdateQuery($this)->getSql();
    }

    public function getParams(): array
    {
        return $this->connection->getDriver()->translateUpdateQuery($this)->getParams();
    }

    public function getTable(): array
    {
        return $this->table;
    }

    public function getValues(): array
    {
        return $this->values;
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
