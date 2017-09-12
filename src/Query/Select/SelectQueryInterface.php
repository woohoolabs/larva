<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

interface SelectQueryInterface
{
    public function getSelectExpressions(): array;

    public function isDistinct(): bool;

    public function getFrom(): array;

    public function getJoins(): array;

    public function getWhere(): ?ConditionsInterface;

    public function getGroupBy(): array;

    public function getHaving(): ?ConditionsInterface;

    public function getOrderBy(): array;

    public function getLock(): array;

    public function getLimit(): ?int;

    public function getOffset(): ?int;

    public function getUnions(): array;
}
