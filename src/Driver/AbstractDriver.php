<?php

declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Query\Delete\DeleteQueryInterface;
use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;
use WoohooLabs\Larva\Query\Select\SelectQueryInterface;
use WoohooLabs\Larva\Query\Truncate\TruncateQueryInterface;
use WoohooLabs\Larva\Query\Update\UpdateQueryInterface;

abstract class AbstractDriver implements DriverInterface
{
    private ?SelectTranslatorInterface $selectTranslator;
    private ?InsertTranslatorInterface $insertTranslator;
    private ?UpdateTranslatorInterface $updateTranslator;
    private ?DeleteTranslatorInterface $deleteTranslator;
    private ?TruncateTranslatorInterface $truncateTranslator;

    abstract protected function createSelectTranslator(): SelectTranslatorInterface;

    abstract protected function createInsertTranslator(): InsertTranslatorInterface;

    abstract protected function createUpdateTranslator(): UpdateTranslatorInterface;

    abstract protected function createDeleteTranslator(): DeleteTranslatorInterface;

    abstract protected function createTruncateTranslator(): TruncateTranslatorInterface;

    public function translateSelectQuery(SelectQueryInterface $query): TranslatedQuerySegment
    {
        return $this->getSelectTranslator()->translateSelectQuery($query);
    }

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment
    {
        return $this->getInsertTranslator()->translateInsertQuery($query);
    }

    public function translateUpdateQuery(UpdateQueryInterface $query): TranslatedQuerySegment
    {
        return $this->getUpdateTranslator()->translateUpdateQuery($query);
    }

    public function translateDeleteQuery(DeleteQueryInterface $query): TranslatedQuerySegment
    {
        return $this->getDeleteTranslator()->translateDeleteQuery($query);
    }

    public function translateTruncateQuery(TruncateQueryInterface $query): TranslatedQuerySegment
    {
        return $this->getTruncateTranslator()->translateTruncateQuery($query);
    }

    protected function getSelectTranslator(): SelectTranslatorInterface
    {
        if ($this->selectTranslator === null) {
            $this->selectTranslator = $this->createSelectTranslator();
        }

        return $this->selectTranslator;
    }

    protected function getInsertTranslator(): InsertTranslatorInterface
    {
        if ($this->insertTranslator === null) {
            $this->insertTranslator = $this->createInsertTranslator();
        }

        return $this->insertTranslator;
    }

    protected function getUpdateTranslator(): UpdateTranslatorInterface
    {
        if ($this->updateTranslator === null) {
            $this->updateTranslator = $this->createUpdateTranslator();
        }

        return $this->updateTranslator;
    }

    protected function getDeleteTranslator(): DeleteTranslatorInterface
    {
        if ($this->deleteTranslator === null) {
            $this->deleteTranslator = $this->createDeleteTranslator();
        }

        return $this->deleteTranslator;
    }

    protected function getTruncateTranslator(): TruncateTranslatorInterface
    {
        if ($this->truncateTranslator === null) {
            $this->truncateTranslator = $this->createTruncateTranslator();
        }

        return $this->truncateTranslator;
    }
}
