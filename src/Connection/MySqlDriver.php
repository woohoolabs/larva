<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver;

use WoohooLabs\Larva\Driver\Mysql\MySqlDeleteTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlInsertTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlSelectTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlTruncateTranslator;
use WoohooLabs\Larva\Driver\Mysql\MySqlUpdateTranslator;

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
}
