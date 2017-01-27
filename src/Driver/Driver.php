<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;
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

    /**
     * @var DeleteTranslatorInterface
     */
    private $deleteTranslator;

    public function __construct(
        SelectTranslatorInterface $selectTranslator,
        InsertTranslatorInterface $insertTranslator,
        UpdateTranslatorInterface $updateTranslator,
        DeleteTranslatorInterface $deleteTranslator
    ) {
        $this->selectTranslator = $selectTranslator;
        $this->insertTranslator = $insertTranslator;
        $this->updateTranslator = $updateTranslator;
        $this->deleteTranslator = $deleteTranslator;
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

    public function translateDeleteQuery(DeleteQueryInterface $query): TranslatedQuerySegment
    {
        return $this->deleteTranslator->translateDeleteQuery($query);
    }
}
