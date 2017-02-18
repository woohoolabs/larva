<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query;

use WoohooLabs\Larva\Connection\ConnectionInterface;

interface DmlCommandBuilderInterface extends QueryBuilderInterface
{
    public function execute(ConnectionInterface $connection): bool;
}
