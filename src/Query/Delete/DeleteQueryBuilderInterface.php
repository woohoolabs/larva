<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\DmlCommandBuilderInterface;

interface DeleteQueryBuilderInterface extends DmlCommandBuilderInterface
{
    public function from(string $table): DeleteQueryBuilderInterface;

    public function where(ConditionBuilderInterface $where): DeleteQueryBuilderInterface;

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): DeleteQueryBuilderInterface;

    public function toQuery(): DeleteQueryInterface;
}
