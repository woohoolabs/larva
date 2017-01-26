<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

class TranslatedQuerySegment
{
    /**
     * @var string
     */
    private $sql;

    /**
     * @var array
     */
    private $params;

    public function __construct($sql = "", array $params = [])
    {
        $this->sql = $sql;
        $this->params = $params;
    }

    public function add(string $sql, array $params = [])
    {
        $this->sql .= $sql;
        $this->params = array_merge($this->params, $params);
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function getParams(): array
    {
        return $this->params;
    }
}
