<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Update;

use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\DmlCommandBuilderInterface;

interface UpdateQueryBuilderInterface extends DmlCommandBuilderInterface
{
    public function table(string $table, string $alias = ""): UpdateQueryBuilderInterface;

    /**
     * @param mixed $value
     */
    public function setValue(string $column, $value): UpdateQueryBuilderInterface;

    public function setValues(array $values): UpdateQueryBuilderInterface;

    public function setRawValue(string $column, string $value, array $params = []): UpdateQueryBuilderInterface;

    public function setRawValues(array $values, array $params = []): UpdateQueryBuilderInterface;

    public function where(ConditionBuilderInterface $where): UpdateQueryBuilderInterface;

    public function addWhereGroup(ConditionBuilderInterface $where, string $operator = "AND"): UpdateQueryBuilderInterface;

    public function toQuery(): UpdateQueryInterface;
}
