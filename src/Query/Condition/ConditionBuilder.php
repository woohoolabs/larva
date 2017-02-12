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

    public function columnToExpression(
        string $column,
        string $operator,
        string $expression,
        array $params = [],
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "column-expression",
            "prefix" => $columnPrefix,
            "column" => $column,
            "operator" => $operator,
            "expression" => $expression,
            "params" => $params,
        ];

        return $this;
    }

    public function expressionToExpression(
        string $expression1,
        string $operator,
        string $expression2,
        array $params = []
    ): ConditionBuilderInterface {
        $this->conditions[] = [
            "type" => "expression-expression",
            "expression1" => $expression1,
            "operator" => $operator,
            "expression2" => $expression2,
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
        $selectBuilder = new SelectQueryBuilder($this->connection);
        $subselect($selectBuilder);

        $this->conditions[] = [
            "type" => "in-subselect",
            "prefix" => $columnPrefix,
            "column" => $column,
            "subselect" => $selectBuilder,
            "not" => false,
        ];

        return $this;
    }

    public function notInSubselect(
        string $column,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $selectBuilder = new SelectQueryBuilder($this->connection);
        $subselect($selectBuilder);

        $this->conditions[] = [
            "type" => "in-subselect",
            "column" => $column,
            "subselect" => $selectBuilder,
            "not" => true,
        ];

        return $this;
    }

    public function exists(Closure $subselect): ConditionBuilderInterface
    {
        $selectBuilder = new SelectQueryBuilder($this->connection);
        $subselect($selectBuilder);

        $this->conditions[] = [
            "type" => "exists",
            "subselect" => $subselect,
            "not" => false,
        ];

        return $this;
    }

    public function notExists(Closure $subselect): ConditionBuilderInterface
    {
        $selectBuilder = new SelectQueryBuilder($this->connection);
        $subselect($selectBuilder);

        $this->conditions[] = [
            "type" => "exists",
            "subselect" => $selectBuilder,
            "not" => true,
        ];

        return $this;
    }

    public function some(
        string $column,
        string $operator,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        return $this->quantify("SOME", $column, $operator, $subselect, $columnPrefix);
    }

    public function any(
        string $column,
        string $operator,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        return $this->quantify("ANY", $column, $operator, $subselect, $columnPrefix);
    }

    public function all(
        string $column,
        string $operator,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        return $this->quantify("ALL", $column, $operator, $subselect, $columnPrefix);
    }

    private function quantify(
        string $mode,
        string $column,
        string $operator,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface {
        $selectBuilder = new SelectQueryBuilder($this->connection);
        $subselect($selectBuilder);

        $this->conditions[] = [
            "type" => "quantification",
            "mode" => $mode,
            "prefix" => $columnPrefix,
            "column" => $column,
            "operator" => $operator,
            "subselect" => $selectBuilder,
        ];

        return $this;
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
