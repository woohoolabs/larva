<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

abstract class AbstractQueryTranslator
{
    protected function compileTranslatedClauses(array $clauses): TranslatedQuerySegment
    {
        $query = new TranslatedQuerySegment();

        foreach ($clauses as $name => $segments) {
            foreach ($segments as $segment) {
                /** @var TranslatedQuerySegment $segment */
                if (empty($segment->getSql())) {
                    continue;
                }

                $query->add($segment->getSql() . "\n", $segment->getParams());
            }
        }

        return $query;
    }

    protected function createTranslatedClause(string $name = "", string $sql = "", array $params = []): TranslatedQuerySegment
    {
        return new TranslatedQuerySegment($name . "\n\t" . $sql, $params);
    }
}
