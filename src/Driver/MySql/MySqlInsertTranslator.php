<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\InsertTranslatorInterface;
use WoohooLabs\Larva\Driver\SelectTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;

class MySqlInsertTranslator extends AbstractQueryTranslator implements InsertTranslatorInterface
{
    /**
     * @var SelectTranslatorInterface
     */
    private $selectTranslator;

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

    private function translateInto(InsertQueryInterface $query)
    {
        $into = $query->getInto();
        $columns = "";

        foreach ($query->getColumns() as $column) {
            $columns .= "`$column`, ";
        }
        $columns = rtrim($columns, ", ");

        implode(",", $query->getColumns());

        return [
            $this->createTranslatedClause("INSERT INTO", "`$into` ($columns)")
        ];
    }

    private function translateValues(InsertQueryInterface $query)
    {
        $values = "";
        $params = [];

        foreach ($query->getValues() as $row) {
            $values .= "(" . implode(",", array_fill(0, count($row), "?")) . "),";
            $params = array_merge($params, array_values($row));
        }
        $values = rtrim($values, ",");

        return [
            $this->createTranslatedClause("VALUES", $values, $params)
        ];
    }

    private function translateSelect(InsertQueryInterface $query): array
    {
        $select = $query->getSelect();

        if ($select === null) {
            return [];
        }

        return [
            $this->selectTranslator->translateSelectQuery($select)
        ];
    }
}
