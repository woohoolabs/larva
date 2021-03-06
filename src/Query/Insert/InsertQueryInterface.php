<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Insert;

use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

interface InsertQueryInterface
{
    public function getInto(): string;

    public function getColumns(): array;

    public function getValues(): array;

    public function getSelect(): ?SelectQueryInterface;
}
