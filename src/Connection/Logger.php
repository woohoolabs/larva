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

    public function getTime(): ?float
    {
        if ($this->isEnabled === false) {
            return null;
        }

        return microtime(true);
    }

    public function log(string $sql, bool $result, array $params = [], float $started = null, float $ended = null): void
    {
        if ($this->isEnabled === false) {
            return;
        }

        $this->log[] = [
            "time" => date("Y-m-d H:i:s"),
            "duration" => $started && $ended ? ($ended - $started) * 1000 : null,
            "result" => $result,
            "sql" => $sql,
            "params" => $params,
        ];
    }

    public function getLog(): array
    {
        return $this->log;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }
}
