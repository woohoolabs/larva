<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Select;

use WoohooLabs\Larva\Query\Condition\ConditionsInterface;
use WoohooLabs\Larva\Query\QueryInterface;

interface SelectQueryInterface extends QueryInterface
{
    public function getSelectExpressions(): array;

    public function isDistinct(): bool;

    public function getFrom(): array;

    public function getJoins(): array;

    public function getWhere(): ConditionsInterface;

    public function getGroupBy(): array;

    public function getHaving(): ConditionsInterface;

    public function getOrderBy(): array;

    public function getLock(): array;

    /**
     * @return int|null
     */
    public function getLimit();

    /**
     * @return int|null
     */
    public function getOffset();
}
