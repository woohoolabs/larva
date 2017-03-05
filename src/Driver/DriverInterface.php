<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;
use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;
use WoohooLabs\Larva\Query\Truncate\TruncateQueryInterface;
use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

interface DriverInterface
{
    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment;

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment;

    public function translateUpdateQuery(UpdateQueryInterface $query): TranslatedQuerySegment;

    public function translateDeleteQuery(DeleteQueryInterface $query): TranslatedQuerySegment;

    public function translateTruncateQuery(TruncateQueryInterface $query): TranslatedQuerySegment;
}
