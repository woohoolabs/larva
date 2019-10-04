<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Truncate\TruncateQueryInterface;

interface TruncateTranslatorInterface
{
    public function translateTruncateQuery(TruncateQueryInterface $query): TranslatedQuerySegment;
}
