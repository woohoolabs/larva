<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
mb_http_output('UTF-8');

require __DIR__ . "/../vendor/autoload.php";

use WoohooLabs\Larva\Connection\ConnectionFactory;
use WoohooLabs\Larva\Query\Condition\ConditionBuilder;
use WoohooLabs\Larva\Query\Delete\DeleteQueryBuilder;
use WoohooLabs\Larva\Query\Insert\InsertQueryBuilder;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilder;
use WoohooLabs\Larva\Query\Update\UpdateQueryBuilder;

$connectionFactory = ConnectionFactory::createFromFile("config.php");
$connection = $connectionFactory->createConnection("default");

$result1 = SelectQueryBuilder::create()
    ->selectColumn("*", "s")
    ->from("students", "s")
    ->where(
        ConditionBuilder::create()
            ->raw("`last_name` LIKE ?", ["%a%"])
            ->and()
            ->nested(
                ConditionBuilder::create()
                    ->is("birthday", null, "s")
                    ->or()
                    ->is("gender", null, "s")
            )
    )
    ->addWhereGroup(
        ConditionBuilder::create()
            ->raw("1 = 1")
    )
    ->limit(10)
    ->offset(0)
    ->lockForShare()
    ->fetchAll($connection);

$result2 = SelectQueryBuilder::create()
    ->selectColumns(["*"], "s")
    ->distinct()
    ->from("courses", "c")
    ->join("classes", "cl")
    ->on(
        ConditionBuilder::create()
            ->raw("c.id = cl.course_id")
    )
    ->join("classes_students", "cs")
    ->on(
        ConditionBuilder::create()
            ->raw("cl.id = cs.class_id")
    )
    ->join("students", "s")
    ->on(
        ConditionBuilder::create()
            ->raw("s.id = cs.student_id")
    )
    ->where(
        ConditionBuilder::create()
            ->raw("c.id = ?", [2])
    )
    ->orderByAttribute("s.id", "ASC")
    ->fetchAll($connection);

InsertQueryBuilder::create()
    ->into("students")
    ->columns(["first_name", "last_name", "birthday", "gender", "introduction"])
    ->values(["John", "Snow", "1970-01-01", "MALE", ""])
    ->values(["John", "Connor", "1971-01-01", "MALE", ""])
    ->execute($connection);

UpdateQueryBuilder::create()
    ->table("students")
    ->setValues(
        [
            "first_name" => "John",
            "last_name" => "Snow",
            "birthday" => "1970-01-01",
        ]
    )
    ->where(
        ConditionBuilder::create()
            ->raw("id = ?", [1])
    )
    ->execute($connection);

DeleteQueryBuilder::create()
    ->from("students")
    ->where(
        ConditionBuilder::create()
            ->raw("id = 1")
    )
    ->execute($connection);

echo "<h1>LOG:</h1>";
echo "<pre>";
print_r($connection->getLog());

echo "<h1>RESULT SET 1:</h1>";
print_r($result1);

echo "<h1>RESULT SET 2:</h1>";
print_r($result2);
echo "</pre>";
