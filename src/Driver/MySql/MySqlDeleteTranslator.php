<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\MySql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\DeleteTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;

class MySqlDeleteTranslator extends AbstractQueryTranslator implements DeleteTranslatorInterface
{
    private MySqlSelectTranslator $selectTranslator;

    public function __construct(MySqlSelectTranslator $conditionsTranslator)
    {
        $this->selectTranslator = $conditionsTranslator;
    }

    public function translateDeleteQuery(DeleteQueryInterface $query): TranslatedQuerySegment
    {
        return $this->compileTranslatedClauses(
            [
                $this->translateFrom($query),
                $this->translateWhere($query),
            ]
        );
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateFrom(DeleteQueryInterface $query): array
    {
        $from = $query->getFrom();

        return [
            $this->createTranslatedClause("DELETE FROM", "`$from`"),
        ];
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateWhere(DeleteQueryInterface $query): array
    {
        $where = $query->getWhere();

        if ($where === null) {
            return [];
        }

        $querySegment = $this->selectTranslator->translateConditions($where);

        return [
            $this->createTranslatedClause("WHERE", $querySegment->getSql(), $querySegment->getParams()),
        ];
    }
}
