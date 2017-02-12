<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Insert;

use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\QueryBuilderInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilderInterface;

interface InsertQueryBuilderInterface extends QueryBuilderInterface
{
    public function into(string $table): InsertQueryBuilderInterface;

    public function columns(array $columns): InsertQueryBuilderInterface;

    public function values(array $values): InsertQueryBuilderInterface;

    public function multipleValues(array $values): InsertQueryBuilderInterface;

    public function select(SelectQueryBuilderInterface $select): InsertQueryBuilderInterface;

    public function execute(ConnectionInterface $connection): bool;

    public function getQuery(): InsertQueryInterface;
}
