<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Condition;

use Closure;
use WoohooLabs\Larva\Connection\ConnectionInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilder;

class ConditionBuilder implements ConditionBuilderInterface, ConditionsInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var array
     */
    private $conditions = [];

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function columnToValue(
        string $column,
        string $operator,
        $value,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "column-value",
            "prefix" => $columnPrefix,
            "column" => $column,
            "operator" => $operator,
            "value" => $value,
        ];

        return $this;
    }

    public function columnToColumn(
        string $column1,
        string $operator,
        string $column2,
        string $column1Prefix = "",
        string $column2Prefix = ""
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "column-column",
            "prefix1" => $column1Prefix,
            "column1" => $column1,
            "operator" => $operator,
            "prefix2" => $column2Prefix,
            "column2" => $column2,
        ];

        return $this;
    }

    public function columnToFunction(
        string $column,
        string $operator,
        string $function,
        array $params = [],
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "column-function",
            "prefix" => $columnPrefix,
            "column" => $column,
            "operator" => $operator,
            "function" => $function
        ];

        return $this;
    }

    public function functionToFunction(
        string $function1,
        string $operator,
        string $function2,
        array $params = []
    ): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "function-function",
            "function1" => $function1,
            "operator" => $operator,
            "function2" => $function2,
            "params" => $params,
        ];

        return $this;
    }

    public function is(string $column, $value, string $columnPrefix = ""): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "is",
            "prefix" => $columnPrefix,
            "column" => $column,
            "value" => $value,
            "not" => false,
        ];

        return $this;
    }

    public function isNot(string $column, $value, string $columnPrefix = ""): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "is",
            "prefix" => $columnPrefix,
            "column" => $column,
            "value" => $value,
            "not" => true,
        ];

        return $this;
    }

    public function inValues(string $column, array $values, string $columnPrefix = ""): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "in-values",
            "prefix" => $columnPrefix,
            "column" => $column,
            "values" => $values,
            "not" => false,
        ];

        return $this;
    }

    public function notInValues(string $column, array $values, string $columnPrefix = ""): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "in-values",
            "prefix" => $columnPrefix,
            "column" => $column,
            "values" => $values,
            "not" => true,
        ];

        return $this;
    }

    public function inSubselect(
        string $column,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "in-subselect",
            "prefix" => $columnPrefix,
            "column" => $column,
            "subselect" => $subselect,
            "not" => false,
        ];
    }

    public function notInSubselect(
        string $column,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "in-subselect",
            "column" => $column,
            "subselect" => $subselect,
            "not" => true,
        ];
    }

    public function raw(string $condition, array $params = []): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "raw",
            "condition" => $condition,
            "params" => $params,
        ];

        return $this;
    }

    public function nested(Closure $condition): ConditionBuilderInterface
    {
        $conditionBuilder = new ConditionBuilder($this->connection);
        $condition($conditionBuilder);

        $this->conditions[] = [
            "type" => "nested",
            "condition" => $conditionBuilder,
        ];

        return $this;
    }

    public function subselect(string $operator, Closure $subselect): ConditionBuilderInterface
    {
        $subselectBuilder = new SelectQueryBuilder($this->connection);

        $subselect($subselectBuilder);

        $this->conditions[] = [
            "type" => "subselect",
            "condition" => $subselectBuilder,
            "operator" => $operator,
        ];

        return $this;
    }

    public function and(): ConditionBuilderInterface
    {
        return $this->operator("AND");
    }

    public function or(): ConditionBuilderInterface
    {
        return $this->operator("OR");
    }

    public function operator(string $operator): ConditionBuilderInterface
    {
        $this->conditions[] = [
            "type" => "operator",
            "operator" => $operator,
        ];

        return $this;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }
}
