<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Delete;

use Closure;
use WoohooLabs\Larva\Query\QueryBuilderInterface;

interface DeleteQueryBuilderInterface extends QueryBuilderInterface
{
    public function from(string $table): DeleteQueryBuilderInterface;

    public function where(Closure $where): DeleteQueryBuilderInterface;

    public function execute(): bool;
}
