<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

interface UpdateTranslatorInterface
{
    public function translateUpdateQuery(UpdateQueryInterface $query): TranslatedQuerySegment;
}
