<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\AbstractQueryTranslator;
use WoohooLabs\Larva\Driver\SelectTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

class MySqlSelectTranslator extends AbstractQueryTranslator implements SelectTranslatorInterface
{
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
                $this->translateLock($query),
            ]
        );
    }

    public function translateConditions(ConditionsInterface $conditions): TranslatedQuerySegment
    {
        $querySegment = new TranslatedQuerySegment();

        $conditionArray = $conditions->getConditions();
        foreach ($conditionArray as $condition) {
            switch ($condition["type"]) {
                case "column-value":
                    $this->translateColumnToValueCondition($querySegment, $condition);
                    break;
                case "column-column":
                    $this->translateColumnToColumnCondition($querySegment, $condition);
                    break;
                case "column-expression":
                    $this->translateColumnToExpressionCondition($querySegment, $condition);
                    break;
                case "expression-expression":
                    $this->translateExpressionToExpressionCondition($querySegment, $condition);
                    break;
                case "is":
                    $this->translateIsCondition($querySegment, $condition);
                    break;
                case "in-values":
                    $this->translateInValues($querySegment, $condition);
                    break;
                case "in-subselect":
                    $this->translateInSubselect($querySegment, $condition);
                    break;
                case "exists":
                    $this->translateExists($querySegment, $condition);
                    break;
                case "quantification":
                    $this->translateQuantification($querySegment, $condition);
                    break;
                case "raw":
                    $this->translateRawCondition($querySegment, $condition);
                    break;
                case "nested":
                    $this->translateNestedCondition($querySegment, $condition);
                    break;
                case "operator":
                    $this->translateOperator($querySegment, $condition);
                    break;
            }
        }

        return $querySegment;
    }

    private function translateSelect(SelectQueryInterface $query): TranslatedQuerySegment
    {
        $distinct = $query->isDistinct() ? " DISTINCT" : "";

        $selectExpressions = $this->getSelectExpressions($query);

        if (empty($selectExpressions)) {
            return $this->createTranslatedClause("SELECT$distinct", "*");
        }

        return $this->createTranslatedClause("SELECT$distinct", implode(",", $selectExpressions));
    }

    private function getSelectExpressions(SelectQueryInterface $query): array
    {
        $expressions = [];

        foreach ($query->getSelectExpressions() as $item) {
            $prefix = $item["prefix"] ? "`" . $item["prefix"] . "`." : "";

            if (($item["type"] === "column" || $item["type"] === "count") && $item["expression"] !== "*") {
                $expression = "`" . $item["column"] . "`";
            } else {
                $expression = $item["expression"];
            }

            $alias = $item["alias"] ? " AS `" . $item["alias"] . "`" : "";

            if ($item["type"] === "count") {
                $distinct = $item["distinct"] ? "DISTINCT " : "";
                $select = "COUNT($distinct$prefix$expression)$alias";
            } else {
                $select = "$prefix$expression$alias";
            }

            $expressions[] = $select;
        }

        return $expressions;
    }

    private function translateFrom(SelectQueryInterface $query): array
    {
        $from = $query->getFrom();

        if (empty($from)) {
            return [];
        }

        $alias = empty($from["alias"]) ? "" : " AS `" . $from["alias"] . "`";

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
                $conditionSegment = $this->translateConditions($join["on"]);
                $params = $conditionSegment->getParams();

                $on = $conditionSegment->getSql();

                $segments[] = $this->createTranslatedClause("ON", "($on)", $params);
            } else {
                $type = $join["type"] ? $join["type"] : "";
                $table = $join["table"];
                $alias = empty($join["alias"]) ? "" : " AS `" . $join["alias"] . "`";

                $segments[] = $this->createTranslatedClause("${type}JOIN", "`${table}`${alias}", $params);
            }
        }

        return $segments;
    }

    private function translateWhere(SelectQueryInterface $query): array
    {
        $where = $query->getWhere();

        if ($where === null) {
            return [];
        }

        $result = $this->translateConditions($where);

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
        $having = $query->getHaving();

        if ($having === null) {
            return [];
        }

        $result = $this->translateConditions($having);

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

    private function translateLock(SelectQueryInterface $query): array
    {
        $lock = $query->getLock();

        if (empty($lock)) {
            return [];
        }

        $mode = "";
        switch ($lock["type"]) {
            case "share":
                $mode = "LOCK IN SHARE MODE";
                break;
            case "update":
                $mode = "FOR UPDATE";
                break;
            case "custom":
                $mode = $lock["mode"];
                break;
        }

        return [
            new TranslatedQuerySegment($mode)
        ];
    }

    private function translateColumnToValueCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $operator = $condition["operator"];
        $value = $condition["value"];

        $querySegment->add("$prefix`$column` $operator ?", [$value]);
    }

    private function translateColumnToColumnCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix1 = $condition["prefix1"] ? "`" . $condition["prefix1"] . "`." : "";
        $column1 = $condition["column1"];
        $operator = $condition["operator"];
        $prefix2 = $condition["prefix2"] ? "`" . $condition["prefix2"] . "`." : "";
        $column2 = $condition["column2"];

        $querySegment->add("$prefix1`$column1` $operator $prefix2`$column2`");
    }

    private function translateColumnToExpressionCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $operator = $condition["operator"];
        $expression = $condition["expression"];

        $querySegment->add("$prefix`$column` $operator $expression", $condition["params"]);
    }

    private function translateExpressionToExpressionCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $expression1 = $condition["expression1"];
        $operator = $condition["operator"];
        $expression2 = $condition["expression2"];

        $querySegment->add("$expression1 $operator $expression2", $condition["params"]);
    }

    private function translateIsCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $negation = $condition["not"] ? " NOT" : "";
        $value = isset($condition["value"]) ? $condition["value"] : "NULL";

        $querySegment->add("$prefix`$column` IS$negation $value");
    }

    private function translateInValues(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $negation = $condition["not"] ? "NOT " : "";
        $values = $condition["values"];
        $valuePattern = implode(",", array_fill(0, count($values), "?"));

        $querySegment->add("$prefix`$column` ${negation}IN ($valuePattern)", $values);
    }

    private function translateInSubselect(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $negation = $condition["not"] ? "NOT " : "";

        $subselect = $this->translateSelectQuery($condition["subselect"]);
        $subselectSql = $subselect->getSql();

        $querySegment->add("$prefix`$column` ${negation}IN ($subselectSql)", $subselect->getParams());
    }

    private function translateExists(TranslatedQuerySegment $querySegment, array $condition)
    {
        $negation = $condition["not"] ? "NOT " : "";

        $subselect = $this->translateSelectQuery($condition["subselect"]);
        $subselectSql = $subselect->getSql();

        $querySegment->add("${negation}EXISTS ($subselectSql)", $subselect->getParams());
    }

    private function translateQuantification(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $operator = $condition["operator"];
        $mode = $condition["mode"];

        $subselect = $this->translateSelectQuery($condition["subselect"]);
        $subselectSql = $subselect->getSql();

        $querySegment->add("$prefix`$column` $operator $mode ($subselectSql)", $subselect->getParams());
    }

    private function translateRawCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $querySegment->add($condition["condition"], $condition["params"]);
    }

    private function translateNestedCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $nestedSegment = $this->translateConditions($condition["condition"]);

        $querySegment->add("(" . $nestedSegment->getSql() . ")", $nestedSegment->getParams());
    }

    private function translateOperator(TranslatedQuerySegment $querySegment, array $operator)
    {
        $querySegment->add(" " . $operator["operator"] . " ");
    }
}
