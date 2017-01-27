<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\SelectTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

class MySqlSelectTranslator extends AbstractQueryTranslator implements SelectTranslatorInterface
{
    /**
     * @var MySqlConditionsTranslator;
     */
    private $conditionsTranslator;

    public function __construct(MySqlConditionsTranslator $conditionsTranslator)
    {
        $this->conditionsTranslator = $conditionsTranslator;
    }

    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment
    {
        return $this->compileTranslatedClauses(
            [
                [$this->translateSelect($query)],
                $this->translateFrom($query),
                $this->translateJoins($query),
                $this->translateWhere($query),
                $this->translateGroupBy($query),
                $this->translateHaving($query),
                $this->translateOrderBy($query),
                $this->translateLimit($query),
                $this->translateOffset($query),
            ]
        );
    }

    private function translateSelect(SelectQueryInterface $query): TranslatedQuerySegment
    {
        $distinct = $query->isDistinct() ? " DISTINCT" : "";

        if (empty($query->getSelect())) {
            return $this->createTranslatedClause("SELECT$distinct", "*");
        }

        return $this->createTranslatedClause("SELECT$distinct", implode(",", $query->getSelect()));
    }

    private function translateFrom(SelectQueryInterface $query): array
    {
        $from = $query->getFrom();

        if (empty($from)) {
            return [];
        }

        $alias = empty($from["alias"]) ? "" : " AS " . $from["alias"];

        if ($from["type"] === "subquery") {
            $subselectSegment = $this->translateSelect($from["from"]);
            $subselect = $subselectSegment->getSql();

            return [
                $this->createTranslatedClause("FROM", "($subselect)$alias", $subselectSegment->getParams())
            ];
        }

        $table = $from["table"];

        return [
            $this->createTranslatedClause("FROM", "`$table`$alias")
        ];
    }

    /**
     * @param SelectQueryInterface $query
     * @return TranslatedQuerySegment[]
     */
    private function translateJoins(SelectQueryInterface $query): array
    {
        $joins = $query->getJoins();

        if (empty($joins)) {
            return [];
        }

        $segments = [];
        $params = [];
        foreach ($joins as $join) {
            if (isset($join["on"])) {
                $conditionSegment = $this->conditionsTranslator->translateConditions($join["on"]);
                $params = $conditionSegment->getParams();

                $on = $conditionSegment->getSql();

                $segments[] = $this->createTranslatedClause("ON", "$on", $params);
            } else {
                $type = $join["type"] ? $join["type"] : "";
                $table = $join["table"];
                $alias = empty($join["alias"]) ? "" : " AS " . $join["alias"];

                $segments[] = $this->createTranslatedClause("${type}JOIN", "`${table}`${alias}", $params);
            }
        }

        return $segments;
    }

    private function translateWhere(SelectQueryInterface $query): array
    {
        if (empty($query->getWhere()->getConditions())) {
            return [];
        }

        $result = $this->conditionsTranslator->translateConditions($query->getWhere());

        return [
            $this->createTranslatedClause("WHERE", $result->getSql(), $result->getParams())
        ];
    }

    private function translateGroupBy(SelectQueryInterface $query): array
    {
        if (empty($query->getGroupBy())) {
            return [];
        }

        return [
            $this->createTranslatedClause("GROUP BY", implode(",", $query->getGroupBy()))
        ];
    }

    private function translateHaving(SelectQueryInterface $query): array
    {
        if (empty($query->getHaving()->getConditions())) {
            return [];
        }

        $result = $this->conditionsTranslator->translateConditions($query->getHaving());

        return [
            $this->createTranslatedClause("HAVING", $result->getSql(), $result->getParams())
        ];
    }

    private function translateOrderBy(SelectQueryInterface $query): array
    {
        if (empty($query->getOrderBy())) {
            return [];
        }

        $querySegment = new TranslatedQuerySegment();
        $count = count($query->getOrderBy());
        foreach ($query->getOrderBy() as $i => $orderBy) {
            $attribute = $orderBy["attribute"];
            $direction = $orderBy["direction"] ? " " . $orderBy["direction"] : "";

            $querySegment->add("${attribute}${direction}");

            if ($i < $count - 1) {
                $querySegment->add(", ");
            }
        }

        return [
            $this->createTranslatedClause("ORDER BY", $querySegment->getSql())
        ];
    }

    private function translateLimit(SelectQueryInterface $query): array
    {
        if ($query->getLimit() === null) {
            return [];
        }

        return [
            $this->createTranslatedClause("LIMIT", "?", [$query->getLimit()])
        ];
    }

    private function translateOffset(SelectQueryInterface $query): array
    {
        if ($query->getOffset() === null) {
            return [];
        }

        return [
            $this->createTranslatedClause("OFFSET", "?", [$query->getOffset()])
        ];
    }
}
