<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
mb_http_output('UTF-8');

require __DIR__ . "/../vendor/autoload.php";

use WoohooLabs\Larva\Connection\ConnectionFactory;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\Delete\DeleteQueryBuilder;
use WoohooLabs\Larva\Query\Insert\InsertQueryBuilder;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilder;
use WoohooLabs\Larva\Query\Update\UpdateQueryBuilder;

$connectionFactory = ConnectionFactory::createFromFile("config.php");
$connection = $connectionFactory->createConnection("default");

$result1 = SelectQueryBuilder::create($connection)
    ->selectColumn("*", "s")
    ->from("students", "s")
    ->where(
        function (ConditionBuilderInterface $where) {
            $where
                ->raw("`last_name` LIKE ?", ["%a%"])
                ->and()
                ->nested(
                    function (ConditionBuilderInterface $where) {
                        $where
                            ->is("birthday", null, "s")
                            ->or()
                            ->is("gender", null, "s");
                    }
                );
        }
    )
    ->limit(10)
    ->offset(0)
    ->fetchAll();

$result2 = SelectQueryBuilder::create($connection)
    ->select(["s.*"])
    ->distinct()
    ->from("courses", "c")
    ->join("classes", "cl")
    ->on(
        function (ConditionBuilderInterface $on) {
            $on->raw("c.id = cl.course_id");
        }
    )
    ->join("classes_students", "cs")
    ->on(
        function (ConditionBuilderInterface $on) {
            $on->raw("cl.id = cs.class_id");
        }
    )
    ->join("students", "s")
    ->on(
        function (ConditionBuilderInterface $on) {
            $on->raw("s.id = cs.student_id");
        }
    )
    ->where(
        function (ConditionBuilderInterface $on) {
            $on->raw("c.id = ?", [2]);
        }
    )
    ->orderBy("s.id", "ASC")
    ->fetchAll();

InsertQueryBuilder::create($connection)
    ->into("students")
    ->columns(["first_name", "last_name", "birthday", "gender", "introduction"])
    ->values(["John", "Snow", "1970-01-01", "MALE", ""])
    ->values(["John", "Connor", "1971-01-01", "MALE", ""])
    ->execute();

UpdateQueryBuilder::create($connection)
    ->table("students")
    ->setValues(
        [
            "first_name" => "John",
            "last_name" => "Snow",
            "birthday" => "1970-01-01",
        ]
    )
    ->where(function (ConditionBuilderInterface $where) {
        $where->raw("id = ?", [1]);
    })
    ->execute();

DeleteQueryBuilder::create($connection)
    ->from("students")
    ->where(function (ConditionBuilderInterface $where) {
        $where->raw("id = 1");
    })
    ->execute();

echo "<h1>LOG:</h1>";
echo "<pre>";
print_r($connection->getLog());

echo "<h1>RESULT SET 1:</h1>";
print_r($result1);

echo "<h1>RESULT SET 2:</h1>";
print_r($result2);
echo "</pre>";
