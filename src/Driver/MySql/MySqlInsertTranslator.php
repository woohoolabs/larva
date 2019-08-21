<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\MySql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\InsertTranslatorInterface;
use WoohooLabs\Larva\Driver\SelectTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;
use function array_fill;
use function array_merge;
use function array_values;
use function count;
use function implode;
use function rtrim;

class MySqlInsertTranslator extends AbstractQueryTranslator implements InsertTranslatorInterface
{
    private SelectTranslatorInterface $selectTranslator;

    public function __construct(SelectTranslatorInterface $selectTranslator)
    {
        $this->selectTranslator = $selectTranslator;
    }

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment
    {
        return $this->compileTranslatedClauses(
            [
                $this->translateInto($query),
                $this->translateValues($query),
                $this->translateSelect($query),
            ]
        );
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateInto(InsertQueryInterface $query): array
    {
        $into = $query->getInto();
        $columns = "";

        foreach ($query->getColumns() as $column) {
            $columns .= "`$column`, ";
        }
        $columns = rtrim($columns, ", ");

        implode(",", $query->getColumns());

        return [
            $this->createTranslatedClause("INSERT INTO", "`$into` ($columns)"),
        ];
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateValues(InsertQueryInterface $query): array
    {
        $values = "";
        $params = [];

        foreach ($query->getValues() as $row) {
            $values .= "(" . implode(",", array_fill(0, count($row), "?")) . "),";
            $params = array_merge($params, array_values($row));
        }
        $values = rtrim($values, ",");

        return [
            $this->createTranslatedClause("VALUES", $values, $params),
        ];
    }

    /**
     * @return TranslatedQuerySegment[]
     */
    private function translateSelect(InsertQueryInterface $query): array
    {
        $select = $query->getSelect();

        if ($select === null) {
            return [];
        }

        return [
            $this->selectTranslator->translateSelectQuery($select),
        ];
    }
}
