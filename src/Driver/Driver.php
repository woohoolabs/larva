<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;

class Driver implements DriverInterface
{
    /**
     * @var SelectTranslatorInterface
     */
    private $selectTranslator;

    /**
     * @var InsertTranslatorInterface
     */
    private $insertTranslator;

    public function __construct(
        SelectTranslatorInterface $selectTranslator,
        InsertTranslatorInterface $insertTranslator
    ) {
        $this->selectTranslator = $selectTranslator;
        $this->insertTranslator = $insertTranslator;
    }

    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment
    {
        return $this->selectTranslator->translateSelectQuery($query);
    }

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment
    {
        return $this->insertTranslator->translateInsertQuery($query);
    }
}
