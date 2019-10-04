<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

interface DeleteQueryInterface
{
    public function getFrom(): string;

    public function getWhere(): ?ConditionsInterface;
}
