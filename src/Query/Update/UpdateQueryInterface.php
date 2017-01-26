<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use WoohooLabs\Larva\Query\Condition\ConditionsInterface;
use WoohooLabs\Larva\Query\QueryInterface;

interface UpdateQueryInterface extends QueryInterface
{
    public function getTable(): array;

    public function getValues(): array;

    public function getWhere(): ConditionsInterface;
}
