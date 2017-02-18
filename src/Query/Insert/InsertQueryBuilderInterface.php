<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Insert;

use WoohooLabs\Larva\Query\DmlCommandBuilderInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilderInterface;

interface InsertQueryBuilderInterface extends DmlCommandBuilderInterface
{
    public function into(string $table): InsertQueryBuilderInterface;

    public function columns(array $columns): InsertQueryBuilderInterface;

    public function values(array $values): InsertQueryBuilderInterface;

    public function multipleValues(array $values): InsertQueryBuilderInterface;

    public function select(SelectQueryBuilderInterface $select): InsertQueryBuilderInterface;

    public function toQuery(): InsertQueryInterface;
}
