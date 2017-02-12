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

    /**
     * @return ConditionsInterface|null
     */
    public function getWhere();

    public function getGroupBy(): array;

    /**
     * @return ConditionsInterface|null
     */
    public function getHaving();

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
