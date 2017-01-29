<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;

interface UpdateQueryBuilderInterface
{
    public function table(string $table, string $alias = ""): UpdateQueryBuilderInterface;

    public function setValue(string $column, $value): UpdateQueryBuilderInterface;

    public function setValues(array $values): UpdateQueryBuilderInterface;

    public function setRawValue(string $column, string $value, array $params = []): UpdateQueryBuilderInterface;

    public function setRawValues(array $values, array $params = []): UpdateQueryBuilderInterface;

    public function where(Closure $where): UpdateQueryBuilderInterface;

    public function execute(): bool;

    public function getSql(): string;

    public function getParams(): array;

    public function getConnection(): ConnectionInterface;
}
