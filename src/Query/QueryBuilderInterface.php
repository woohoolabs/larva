<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query;

use WoohooLabs\Larva\Connection\ConnectionInterface;

interface QueryBuilderInterface
{
    public function getSql(ConnectionInterface $connection): string;

    public function getParams(ConnectionInterface $connection): array;
}
