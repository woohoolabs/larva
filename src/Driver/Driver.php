<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;
use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

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

    /**
     * @var UpdateTranslatorInterface
     */
    private $updateTranslator;

    public function __construct(
        SelectTranslatorInterface $selectTranslator,
        InsertTranslatorInterface $insertTranslator,
        UpdateTranslatorInterface $updateTranslator
    ) {
        $this->selectTranslator = $selectTranslator;
        $this->insertTranslator = $insertTranslator;
        $this->updateTranslator = $updateTranslator;
    }

    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment
    {
        return $this->selectTranslator->translateSelectQuery($query);
    }

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment
    {
        return $this->insertTranslator->translateInsertQuery($query);
    }

    public function translateUpdateQuery(UpdateQueryInterface $query): TranslatedQuerySegment
    {
        return $this->updateTranslator->translateUpdateQuery($query);
    }
}
