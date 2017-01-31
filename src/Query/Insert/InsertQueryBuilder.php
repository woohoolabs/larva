<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Insert;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilder;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

class InsertQueryBuilder implements InsertQueryBuilderInterface, InsertQueryInterface
{
    /**
     * @var \WoohooLabs\Larva\Connection\ConnectionInterface
     */
    private $connection;

    /**
     * @var string
     */
    private $into;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $values;

    /**
     * @var SelectQueryInterface
     */
    private $select;

    public static function create(ConnectionInterface $connection): InsertQueryBuilderInterface
    {
        return new InsertQueryBuilder($connection);
    }

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        $this->into = "";
        $this->columns = [];
        $this->values = [];
    }

    public function into(string $table): InsertQueryBuilderInterface
    {
        $this->into = $table;

        return $this;
    }

    public function columns(array $columns): InsertQueryBuilderInterface
    {
        $this->columns = $columns;

        return $this;
    }

    public function values(array $values): InsertQueryBuilderInterface
    {
        $this->values[] = $values;

        return $this;
    }

    public function multipleValues(array $values): InsertQueryBuilderInterface
    {
        $this->values = $values;

        return $this;
    }

    public function select(Closure $select): InsertQueryBuilderInterface
    {
        $this->select = new SelectQueryBuilder($this->connection);

        $select($this->select);

        return $this;
    }

    public function execute(): bool
    {
        $sql = $this->connection->getDriver()->translateInsertQuery($this);

        return $this->connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(): string
    {
        return $this->connection->getDriver()->translateInsertQuery($this)->getSql();
    }

    public function getParams(): array
    {
        return $this->connection->getDriver()->translateInsertQuery($this)->getParams();
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }

    public function getInto(): string
    {
        return $this->into;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return SelectQueryInterface|null
     */
    public function getSelect()
    {
        return $this->select;
    }
}
