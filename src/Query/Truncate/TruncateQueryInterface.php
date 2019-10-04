<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Truncate;

interface TruncateQueryInterface
{
    public function getTable(): string;
}
