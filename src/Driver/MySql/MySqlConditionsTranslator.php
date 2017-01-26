<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Condition\ConditionsInterface;

class MySqlConditionsTranslator
{
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
                case "is":
                    $this->translateIsCondition($querySegment, $condition);
                    break;
                case "in-values":
                    $this->translateInValues($querySegment, $condition);
                    break;
                case "raw":
                    $this->translateRawCondition($querySegment, $condition);
                    break;
                case "nested":
                    $this->translateNestedCondition($querySegment, $condition);
                    break;
                case "subselect":
                    $this->translateSubselectCondition($querySegment, $condition);
                    break;
                case "operator":
                    $this->translateOperator($querySegment, $condition);
                    break;
            }
        }

        return $querySegment;
    }

    private function translateColumnToValueCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix = $condition["prefix"] ? "`" . $condition["prefix"] . "`." : "";
        $column = $condition["column"];
        $operator = $condition["operator"];
        $value = $condition["value"];

        $querySegment->add("$prefix`$column` $operator ?", [$value]);
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

    private function translateColumnToColumnCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $prefix1 = $condition["prefix1"] ? "`" . $condition["prefix1"] . "`." : "";
        $column1 = $condition["column1"];
        $operator = $condition["operator"];
        $prefix2 = $condition["prefix2"] ? "`" . $condition["prefix2"] . "`." : "";
        $column2 = $condition["column2"];

        $querySegment->add("$prefix1`$column1` $operator $prefix2`$column2`");
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

    private function translateSubselectCondition(TranslatedQuerySegment $querySegment, array $condition)
    {
        $subselectSegment = $this->translateConditions($condition["condition"]);

        $querySegment->add("(" . $subselectSegment->getSql() . ")", $subselectSegment->getParams());
    }

    private function translateOperator(TranslatedQuerySegment $querySegment, array $operator)
    {
        $querySegment->add(" " . $operator["operator"] . " ");
    }
}
