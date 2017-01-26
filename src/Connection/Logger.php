<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Connection;

class Logger
{
    /**
     * @var array
     */
    private $log = [];

    /**
     * @var bool
     */
    private $isEnabled;

    public function __construct(bool $isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function log(string $query, bool $result, array $params = [])
    {
        if ($this->isEnabled === false) {
            return;
        }

        $this->log[] = [
            "time" => date("Y-m-d H:i:s"),
            "query" => "$query",
            "params" => $params,
            "result" => $result,
        ];
    }

    public function getLog(): array
    {
        return $this->log;
    }
}
