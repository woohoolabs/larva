<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use Closure;

interface DeleteQueryBuilderInterface
{
    public function from(string $table): DeleteQueryBuilderInterface;

    public function where(Closure $where): DeleteQueryBuilderInterface;

    public function execute(): bool;

    public function getSql(): string;

    public function getParams(): array;
}
