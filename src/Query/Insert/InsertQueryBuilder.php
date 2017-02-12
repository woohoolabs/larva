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
     * @var string
     */
    private $into = "";

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var SelectQueryInterface
     */
    private $select;

    public static function create(): InsertQueryBuilderInterface
    {
        return new InsertQueryBuilder();
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
        $this->select = new SelectQueryBuilder();

        $select($this->select);

        return $this;
    }

    public function execute(ConnectionInterface $connection): bool
    {
        $sql = $connection->getDriver()->translateInsertQuery($this);

        return $connection->execute($sql->getSql(), $sql->getParams());
    }

    public function getSql(ConnectionInterface $connection): string
    {
        return $connection->getDriver()->translateInsertQuery($this)->getSql();
    }

    public function getParams(ConnectionInterface $connection): array
    {
        return $connection->getDriver()->translateInsertQuery($this)->getParams();
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
