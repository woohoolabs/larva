<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;
use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;
use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

class MasterSlaveDriver implements DriverInterface
{
    /**
     * @var DriverInterface
     */
    private $masterDriver;

    /**
     * @var DriverInterface
     */
    private $slaveDriver;

    public function __construct(DriverInterface $masterDriver, DriverInterface $slaveDriver)
    {
        $this->masterDriver = $masterDriver;
        $this->slaveDriver = $slaveDriver;
    }

    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment
    {
        return $this->slaveDriver->translateSelectQuery($query);
    }

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment
    {
        return $this->masterDriver->translateInsertQuery($query);
    }

    public function translateUpdateQuery(UpdateQueryInterface $query): TranslatedQuerySegment
    {
        return $this->masterDriver->translateUpdateQuery($query);
    }

    public function translateDeleteQuery(DeleteQueryInterface $query): TranslatedQuerySegment
    {
        return $this->masterDriver->translateDeleteQuery($query);
    }
}
