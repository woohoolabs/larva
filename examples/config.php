<?php
declare(strict_types=1);

return [
    "default" => [
        "driver" => "mysql",
        "host" => "mysql",
        "port" => (int) getenv("MYSQL_PORT"),
        "database" => getenv("MYSQL_DATABASE"),
        "user" => getenv("MYSQL_USER"),
        "password" => getenv("MYSQL_PASSWORD"),
        "charset" => "utf8mb4",
        "collation" => "utf8mb4_unicode_ci",
        "modes" => [],
        "options" => [],
        "log" => true
    ],
];
