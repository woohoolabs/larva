<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Insert;

use WoohooLabs\Larva\Query\QueryInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

interface InsertQueryInterface extends QueryInterface
{
    public function getInto(): string;

    public function getColumns(): array;

    public function getValues(): array;

    /**
     * @return SelectQueryInterface|null
     */
    public function getSelect();
}
