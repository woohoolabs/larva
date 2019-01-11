<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use function is_bool;

class TranslatedQuerySegment
{
    /**
     * @var string
     */
    private $sql;

    /**
     * @var array
     */
    private $params = [];

    public function __construct(string $sql = "", array $params = [])
    {
        $this->sql = $sql;
        $this->addParams($params);
    }

    public function add(string $sql, array $params = []): void
    {
        $this->sql .= " " . $sql;
        $this->addParams($params);
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    private function addParams(array $params): void
    {
        foreach ($params as $param) {
            if (is_bool($param)) {
                $param = (int) $param;
            }

            $this->params[] = $param;
        }
    }
}
