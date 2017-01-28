<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");
mb_http_output('UTF-8');

require __DIR__ . "/../vendor/autoload.php";

use WoohooLabs\Larva\Connection\MySqlPdoConnection;
use WoohooLabs\Larva\Query\Condition\ConditionBuilderInterface;
use WoohooLabs\Larva\Query\Delete\DeleteQueryBuilder;
use WoohooLabs\Larva\Query\Insert\InsertQueryBuilder;
use WoohooLabs\Larva\Query\Select\SelectQueryBuilder;
use WoohooLabs\Larva\Query\Update\UpdateQueryBuilder;

$connection = MySqlPdoConnection::create(
    "mysql",
    "mysql",
    (int) getenv("MYSQL_PORT"),
    getenv("MYSQL_DATABASE"),
    getenv("MYSQL_USER"),
    getenv("MYSQL_PASSWORD"),
    "utf8mb4",
    "utf8mb4_unicode_ci",
    [],
    [],
    true
);

$query = SelectQueryBuilder::create($connection)
    ->from("students", "s")
    ->where(
        function (ConditionBuilderInterface $where) {
            $where
                ->raw("last_name LIKE ?", ["%a%"])
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
    ->offset(0);

echo "Query:<br/>";
echo "<pre>";
print_r($query->getSql());
echo "</pre>";

echo "Params:<br/>";
echo "<pre>";
print_r($query->getParams());
echo "</pre>";

echo "Result Set:<br/>";
echo "<pre>";
print_r($query->fetchAll());
echo "</pre>";

$query = SelectQueryBuilder::create($connection)
    ->fields(["s.*"])
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
    ->orderBy("s.id", "ASC");

echo "Query:<br/>";
echo "<pre>";
print_r($query->getSql());
echo "</pre>";

echo "Params:<br/>";
echo "<pre>";
print_r($query->getParams());
echo "</pre>";

echo "Result Set:<br/>";
echo "<pre>";
print_r($query->fetchAll());
echo "</pre>";

$query = InsertQueryBuilder::create($connection)
    ->into("students")
    ->columns(["first_name", "last_name", "birthday"])
    ->values(["John", "Snow", "1970-01-01"])
    ->values(["John", "Connor", "1971-01-01"]);

echo "Query:<br/>";
echo "<pre>";
print_r($query->getSql());
echo "</pre>";

$query = UpdateQueryBuilder::create($connection)
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
    });

echo "Query:<br/>";
echo "<pre>";
print_r($query->getSql());
echo "</pre>";

$query = DeleteQueryBuilder::create($connection)
    ->from("students")
    ->where(function (ConditionBuilderInterface $where) {
        $where->raw("id = 1");
    });

echo "Query:<br/>";
echo "<pre>";
print_r($query->getSql());
echo "</pre>";
