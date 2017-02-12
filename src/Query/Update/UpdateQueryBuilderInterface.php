<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\QueryBuilderInterface;

interface UpdateQueryBuilderInterface extends QueryBuilderInterface
{
    public function table(string $table, string $alias = ""): UpdateQueryBuilderInterface;

    public function setValue(string $column, $value): UpdateQueryBuilderInterface;

    public function setValues(array $values): UpdateQueryBuilderInterface;

    public function setRawValue(string $column, string $value, array $params = []): UpdateQueryBuilderInterface;

    public function setRawValues(array $values, array $params = []): UpdateQueryBuilderInterface;

    public function where(ConditionBuilderInterface $where): UpdateQueryBuilderInterface;

    public function execute(ConnectionInterface $connection): bool;

    public function getQuery(): UpdateQueryInterface;
}
