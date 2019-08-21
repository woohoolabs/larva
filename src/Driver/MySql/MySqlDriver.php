<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\MySql;

use WoohooLabs\Larva\Driver\AbstractDriver;
use WoohooLabs\Larva\Driver\DeleteTranslatorInterface;
use WoohooLabs\Larva\Driver\InsertTranslatorInterface;
use WoohooLabs\Larva\Driver\SelectTranslatorInterface;
use WoohooLabs\Larva\Driver\TruncateTranslatorInterface;
use WoohooLabs\Larva\Driver\UpdateTranslatorInterface;

class MySqlDriver extends AbstractDriver
{
    protected function createSelectTranslator(): SelectTranslatorInterface
    {
        return new MySqlSelectTranslator();
    }

    protected function createInsertTranslator(): InsertTranslatorInterface
    {
        return new MySqlInsertTranslator($this->getSelectTranslator());
    }

    protected function createUpdateTranslator(): UpdateTranslatorInterface
    {
        return new MySqlUpdateTranslator($this->getSelectTranslator());
    }

    protected function createDeleteTranslator(): DeleteTranslatorInterface
    {
        return new MySqlDeleteTranslator($this->getSelectTranslator());
    }

    protected function createTruncateTranslator(): TruncateTranslatorInterface
    {
        return new MySqlTruncateTranslator();
    }

    protected function getSelectTranslator(): MySqlSelectTranslator
    {
        return parent::getSelectTranslator();
    }
}
