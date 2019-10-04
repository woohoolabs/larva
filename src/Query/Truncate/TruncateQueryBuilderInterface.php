<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Truncate;

use WoohooLabs\Larva\Query\DmlCommandBuilderInterface;

interface TruncateQueryBuilderInterface extends DmlCommandBuilderInterface
{
    public function table(string $table): TruncateQueryBuilderInterface;

    public function toQuery(): TruncateQueryInterface;
}
