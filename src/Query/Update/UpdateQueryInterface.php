<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

interface UpdateQueryInterface
{
    public function getTable(): array;

    public function getValues(): array;

    public function getWhere(): ?ConditionsInterface;
}
