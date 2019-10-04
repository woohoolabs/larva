<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

interface SelectTranslatorInterface
{
    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment;
}
