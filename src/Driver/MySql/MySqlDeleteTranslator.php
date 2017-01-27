<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\DeleteTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;

class MySqlDeleteTranslator extends AbstractQueryTranslator implements DeleteTranslatorInterface
{
    /**
     * @var MySqlConditionsTranslator
     */
    private $conditionsTranslator;

    public function __construct(MySqlConditionsTranslator $conditionsTranslator)
    {
        $this->conditionsTranslator = $conditionsTranslator;
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

    private function translateFrom(DeleteQueryInterface $query)
    {
        $from = $query->getFrom();

        return [
            $this->createTranslatedClause("DELETE FROM", "`$from`")
        ];
    }

    private function translateWhere(DeleteQueryInterface $query): array
    {
        $where = $query->getWhere();

        if ($where === null) {
            return [];
        }

        $querySegment = $this->conditionsTranslator->translateConditions($where);

        return [
            $this->createTranslatedClause("WHERE", $querySegment->getSql(), $querySegment->getParams())
        ];
    }
}
