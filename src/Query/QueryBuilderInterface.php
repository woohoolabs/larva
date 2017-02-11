<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query;

use WoohooLabs\Larva\Connection\ConnectionInterface;

interface QueryBuilderInterface
{
    public function getSql(): string;

    public function getParams(): array;

    public function getConnection(): ConnectionInterface;
}
