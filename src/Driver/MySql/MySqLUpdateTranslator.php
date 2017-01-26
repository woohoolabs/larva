<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Driver\UpdateTranslatorInterface;
use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

class MySqLUpdateTranslator extends AbstractQueryTranslator implements UpdateTranslatorInterface
{
    /**
     * @var MySqlConditionsTranslator
     */
    private $conditionsTranslator;

    public function __construct(MySqlConditionsTranslator $conditionsTranslator)
    {
        $this->conditionsTranslator = $conditionsTranslator;
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

    private function translateTable(UpdateQueryInterface $query)
    {
        $table = $query->getTable();
        $tableName = $table["table"];
        $alias = empty($table["alias"]) ? "" : " AS " . $table["alias"];

        return [
            $this->createTranslatedClause("UPDATE", "`$tableName`$alias")
        ];
    }

    private function translateValues(UpdateQueryInterface $query)
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
            $this->createTranslatedClause("SET", $values, $params)
        ];
    }

    private function translateWhere(UpdateQueryInterface $query): array
    {
        $where = $query->getWhere();

        if ($where === null) {
            return [];
        }

        return [
            $this->conditionsTranslator->translateConditions($where)
        ];
    }
}
