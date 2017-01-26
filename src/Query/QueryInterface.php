<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query;

use WoohooLabs\Larva\Connection\ConnectionInterface;

interface QueryInterface
{
    public function getConnection(): ConnectionInterface;
}
