<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;

interface DeleteTranslatorInterface
{
    public function translateDeleteQuery(DeleteQueryInterface $query): TranslatedQuerySegment;
}
