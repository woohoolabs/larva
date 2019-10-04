<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;

interface InsertTranslatorInterface
{
    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment;
}
