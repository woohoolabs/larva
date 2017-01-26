#!/usr/bin/env bash
set -e

MYSQL=`which mysql`

# Clear database
Q1="DROP DATABASE IF EXISTS $MYSQL_DATABASE;"
Q2="GRANT USAGE ON $MYSQL_DATABASE.* TO '$MYSQL_USER'@'%';"
Q3="DROP USER '$MYSQL_USER'@'%';"
$MYSQL -h mysql -P 3306 -uroot -p$MYSQL_ROOT_PASSWORD -e "${Q1}${Q2}${Q3}"

# Create database and the corresponding user
Q1="CREATE DATABASE $MYSQL_DATABASE CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
Q2="CREATE USER '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD';"
Q3="GRANT ALL PRIVILEGES ON $MYSQL_DATABASE.* TO '$MYSQL_USER'@'%' IDENTIFIED BY '$MYSQL_PASSWORD';"
Q4="FLUSH PRIVILEGES;"
$MYSQL -h mysql -P 3306 -uroot -p$MYSQL_ROOT_PASSWORD -e "${Q1}${Q2}${Q3}${Q4}"

# Create schema
$MYSQL -h mysql -P 3306 -u$MYSQL_USER -p$MYSQL_PASSWORD $MYSQL_DATABASE < /code/build/db/db.sql
