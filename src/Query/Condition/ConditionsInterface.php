<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Query\Condition;

interface ConditionsInterface
{
    public function getConditions(): array;

    /**
     * @return void
     */
    public function addConditionGroup(ConditionBuilderInterface $conditions, string $operator = "AND");
}
