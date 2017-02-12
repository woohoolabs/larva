<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Insert;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\QueryBuilderInterface;

interface InsertQueryBuilderInterface extends QueryBuilderInterface
{
    public function into(string $table): InsertQueryBuilderInterface;

    public function columns(array $columns): InsertQueryBuilderInterface;

    public function values(array $values): InsertQueryBuilderInterface;

    public function multipleValues(array $values): InsertQueryBuilderInterface;

    public function select(Closure $select): InsertQueryBuilderInterface;

    public function execute(ConnectionInterface $connection): bool;
}
