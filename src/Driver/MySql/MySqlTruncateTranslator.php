<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\MySql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Driver\TruncateTranslatorInterface;
use WoohooLabs\Larva\Query\Truncate\TruncateQueryInterface;

class MySqlTruncateTranslator extends AbstractQueryTranslator implements TruncateTranslatorInterface
{
    public function translateTruncateQuery(TruncateQueryInterface $query): TranslatedQuerySegment
    {
        return $this->compileTranslatedClauses(
            [
                $this->translateTable($query),
            ]
        );
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateTable(TruncateQueryInterface $query): array
    {
        $table = $query->getTable();

        return [
            $this->createTranslatedClause("TRUNCATE ", "`$table`"),
        ];
    }
}
