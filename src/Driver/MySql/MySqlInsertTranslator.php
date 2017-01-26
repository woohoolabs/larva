<?php
declare(strict_types=1);

namespace WoohooLabs\Larva\Driver\Mysql;

use WoohooLabs\Larva\Driver\InsertTranslatorInterface;
use WoohooLabs\Larva\Driver\SelectTranslatorInterface;
use WoohooLabs\Larva\Driver\TranslatedQuerySegment;
use WoohooLabs\Larva\Query\Insert\InsertQueryInterface;

class MySqlInsertTranslator implements InsertTranslatorInterface
{
    /**
     * @var SelectTranslatorInterface
     */
    private $selectTranslator;

    public function __construct(SelectTranslatorInterface $selectTranslator)
    {
        $this->selectTranslator = $selectTranslator;
    }

    public function translateInsertQuery(InsertQueryInterface $query): TranslatedQuerySegment
    {
        return new TranslatedQuerySegment();
    }
}
