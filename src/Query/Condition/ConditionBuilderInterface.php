<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Condition;

use Closure;

interface ConditionBuilderInterface
{
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

    public function columnToFunction(
        string $column,
        string $operator,
        string $function,
        array $params = [],
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function functionToFunction(
        string $function1,
        string $operator,
        string $function2,
        array $params = []
    ): ConditionBuilderInterface;

    public function is(string $column, $value, string $columnPrefix = ""): ConditionBuilderInterface;

    public function isNot(string $column, $value, string $columnPrefix = ""): ConditionBuilderInterface;

    public function inValues(string $column, array $values, string $columnPrefix = ""): ConditionBuilderInterface;

    public function notInValues(string $column, array $value, string $columnPrefix = ""): ConditionBuilderInterface;

    public function inSubselect(
        string $column,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function notInSubselect(
        string $column,
        Closure $subselect,
        string $columnPrefix = ""
    ): ConditionBuilderInterface;

    public function raw(string $condition, array $params = []): ConditionBuilderInterface;

    public function nested(Closure $condition): ConditionBuilderInterface;

    public function subselect(string $operator, Closure $subselect): ConditionBuilderInterface;

    public function and(): ConditionBuilderInterface;

    public function or(): ConditionBuilderInterface;

    public function operator(string $operator): ConditionBuilderInterface;
}
