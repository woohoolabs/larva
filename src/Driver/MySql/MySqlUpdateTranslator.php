<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\MySql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Driver\UpdateTranslatorInterface;
use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

use function array_merge;
use function rtrim;

class MySqlUpdateTranslator extends AbstractQueryTranslator implements UpdateTranslatorInterface
{
    private MySqlSelectTranslator $selectTranslator;

    public function __construct(MySqlSelectTranslator $conditionsTranslator)
    {
        $this->selectTranslator = $conditionsTranslator;
    }

    public function translateUpdateQuery(UpdateQueryInterface $query): TranslatedQuerySegment
    {
        return $this->compileTranslatedClauses(
            [
                $this->translateTable($query),
                $this->translateValues($query),
                $this->translateWhere($query),
            ]
        );
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateTable(UpdateQueryInterface $query): array
    {
        $table = $query->getTable();
        if ($table === []) {
            return [];
        }

        $tableName = $table["table"];
        $alias = $table["alias"] === "" ? "" : " AS `" . $table["alias"] . "`";

        return [
            $this->createTranslatedClause("UPDATE", "`$tableName`$alias"),
        ];
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateValues(UpdateQueryInterface $query): array
    {
        $values = "";
        $params = [];

        foreach ($query->getValues() as $row) {
            $rowColumn = $row["column"];
            $rowValue = $row["type"] === "value" ? "?" : $row["value"];

            $values .= "`$rowColumn` = $rowValue, ";

            if ($row["type"] === "value") {
                $params[] = $row["value"];
            } else {
                $params = array_merge($params, $row["params"]);
            }
        }
        $values = rtrim($values, ", ");

        return [
            $this->createTranslatedClause("SET", $values, $params),
        ];
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateWhere(UpdateQueryInterface $query): array
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
