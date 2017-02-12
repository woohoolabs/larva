<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class DeleteQueryBuilder implements DeleteQueryBuilderInterface, DeleteQueryInterface
{
    /**
     * @var string
     */
    private $from = "";

    /**
     * @var ConditionBuilder
     */
    private $where;

    public static function create(): DeleteQueryBuilderInterface
    {
        return new DeleteQueryBuilder();
    }

    public function __construct()
    {
        $this->where = new ConditionBuilder();
    }

    public function from(string $table): DeleteQueryBuilderInterface
    {
        $this->from = $table;

        return $this;
    }

    public function where(Closure $where): DeleteQueryBuilderInterface
    {
        $this->where = new ConditionBuilder();

        $where($this->where);

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

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getWhere(): ConditionsInterface
    {
        return $this->where;
    }
}
