<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Tests\Connection;

use PHPUnit\Framework\TestCase;
use WoohooLabs\Larva\Connection\Logger;

class LoggerTest extends TestCase
{
    /**
     * @test
     */
    public function getLogWhenDisabled(): void
    {
        $logger = new Logger(false);

        $this->assertEmpty($logger->getLog());
    }

    /**
     * @test
     */
    public function logWhenDisabled(): void
    {
        $logger = new Logger(false);

        $logger->log("SELECT 1", true);

        $this->assertEmpty($logger->getLog());
    }

    /**
     * @test
     */
    public function getTimeWhenDisabled(): void
    {
        $logger = new Logger(false);

        $this->assertNull($logger->getTime());
    }

    /**
     * @test
     */
    public function isEnabledWhenDisabled(): void
    {
        $logger = new Logger(false);

        $this->assertFalse($logger->isEnabled());
    }
}
