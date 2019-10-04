<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Condition;

use WoohooLabs\Larva\Query\Select\SelectQueryBuilderInterface;

interface ConditionBuilderInterface
{
    /**
     * @param mixed $value
     */
    public function columnToValue(
        string $column,
        string $operator,
        $value,
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function columnToColumn(
        string $column1,
        string $operator,
        string $column2,
        string $column1Prefix = "",
        string $column2Prefix = ""
    ): ConditionBuilderInterface;

    public function columnToExpression(
        string $column,
        string $operator,
        string $expression,
        array $params = [],
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function expressionToExpression(
        string $expression1,
        string $operator,
        string $expression2,
        array $params = []
    ): ConditionBuilderInterface;

    /**
     * @param mixed $value
     */
    public function is(string $column, $value, string $columnPrefix = ""): ConditionBuilderInterface;

    /**
     * @param mixed $value
     */
    public function isNot(string $column, $value, string $columnPrefix = ""): ConditionBuilderInterface;

    public function inValues(string $column, array $values, string $columnPrefix = ""): ConditionBuilderInterface;

    public function notInValues(string $column, array $value, string $columnPrefix = ""): ConditionBuilderInterface;

    public function inSubselect(string $column, SelectQueryBuilderInterface $subselect): ConditionBuilderInterface;

    public function notInSubselect(string $column, SelectQueryBuilderInterface $subselect): ConditionBuilderInterface;

    public function exists(SelectQueryBuilderInterface $subselect): ConditionBuilderInterface;

    public function notExists(SelectQueryBuilderInterface $subselect): ConditionBuilderInterface;

    public function some(
        string $column,
        string $operator,
        SelectQueryBuilderInterface $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function any(
        string $column,
        string $operator,
        SelectQueryBuilderInterface $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function all(
        string $column,
        string $operator,
        SelectQueryBuilderInterface $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function raw(string $condition, array $params = []): ConditionBuilderInterface;

    public function nested(ConditionBuilderInterface $condition): ConditionBuilderInterface;

    public function and(): ConditionBuilderInterface;

    public function or(): ConditionBuilderInterface;

    public function operator(string $operator): ConditionBuilderInterface;

    public function toConditions(): ConditionsInterface;
}
