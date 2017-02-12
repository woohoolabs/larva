<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\QueryBuilderInterface;

interface DeleteQueryBuilderInterface extends QueryBuilderInterface
{
    public function from(string $table): DeleteQueryBuilderInterface;

    public function where(ConditionBuilderInterface $where): DeleteQueryBuilderInterface;

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): DeleteQueryBuilderInterface;

    public function execute(ConnectionInterface $connection): bool;

    public function toQuery(): DeleteQueryInterface;
}
